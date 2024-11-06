#!/usr/bin/env python

import sys, time, psycopg2, logging, signal, os, subprocess, re, json
import psycopg2.extras
from pprint import pprint
from daemon import Daemon
from datetime import datetime

import json
import urllib
import urllib2
import requests
import smtplib
import shutil 

from email.mime.text import MIMEText

quit = False


upload_path = '/data/vms_upload/'
data_path   = '/data/vms_data/'
log_path    = '/home/vcg/ariadne.log'

palma    = '/home/vcg/bin/webGLRtiMaker'
relight  = '/home/vcg/bin/relight-cli'
nexus    = '/home/vcg/bin/nxsbuild'
nxsedit  = '/home/vcg/bin/nxsedit'
deepzoom = '/home/vcg/bin/build_deepzoom.sh'
onetile  = 'onetile' 




def sigterm_handler(_signo, _stack_frame):
	global quit
	quit = True


signal.signal(signal.SIGTERM, sigterm_handler)

class Ariadne(Daemon):
	con = None
	cur = None

	def setStatus(self, id, status, error):
		sql = "UPDATE media SET status = %s, error = %s WHERE id = %s"
		self.cur.execute(sql, [status, error, id])
		
		if status == 'ready':
			sql = "UPDATE media SET processed = 1 WHERE id = %s"
			self.cur.execute(sql, [id])
			
		self.con.commit()
		return

	def setThumbnail(self, id, thumbnail):
		sql = "UPDATE media SET thumbnail = %s WHERE id = %s"
		self.cur.execute(sql, [thumbnail, id])
		self.con.commit()
		return

	def setSize(self, id, width, height, mtri=None, mm=None):
		sql = "UPDATE media SET width = %s, height = %s, mtri = %s, mm = %s WHERE id = %s"
		self.cur.execute(sql, [width, height, mtri, mm, id])
		self.con.commit()
		return

	def sendMsgFail(self, media, user):
		text = """Dear %(name)s,
   unfortunately the media (%(title)s) you uploaded could not be processed.

%(error)s

Most probably the media was in a format we do not support, please check under

    http://visual.ariadne-infrastructure.eu/help#supported

for the details of the supported media.

You can still access your media info (and delete it) from:

    http://visual.ariadne-infrastructure.eu/media/%(label)s

If you think the media was the correct format, or for any other reason, please
contact us at

ponchio@isti.cnr.it

Thank you for using our service, and sorry for the trouble

Visual Media Service.""" % media

		self.sendMsg(media, user, '[AMS] Bummer! %s processing failed.' % media["title"], text)




	def sendMsgSuccess(self, media, user):

		text = """Dear %(name)s,
   the media (%(title)s) you uploaded has been processed succesfully.

You can view the result privately here:

    http://visual.ariadne-infrastructure.eu/%(media_type)s/%(secret)s

and if you allowed it publicly here:

    http://visual.ariadne-infrastructure.eu/%(media_type)s/%(label)s

you can edit the related information, download the result, or delete it here:

    http://visual.ariadne-infrastructure.eu/media/%(label)s

Thank you for using our service, 

Visual Media Service.

P.S. If you need to contact us write to: ponchio@isti.cnr.it""" %  media

		self.sendMsg(media, user, '[AMS] %s is ready.' % media["title"], text)


	def sendMsg(self, media, user, subject, text):
		if user["sendemail"] != 1:
			return

		if user["d4science"]:
			self.send4science(media, user["d4science"]["token"], subject, text)
		else:
			self.sendEmail(media["email"], subject, text)
		self.sendEmail("ponchio@gmail.com", subject, text)


	def sendEmail(self, email, subject, text):
		logging.debug("Sending mail to %s" % email)
		# Create a text/plain message
		msg = MIMEText(text)

		# me == the sender's email address
		# you == the recipient's email address
		msg['Subject'] = subject
		msg['From'] = 'ponchio@isti.cnr.it'
		msg['To'] = email

		smtp = smtplib.SMTP('smtp-out.isti.cnr.it', 587)
		try:

			smtp.set_debuglevel(0)
			smtp.ehlo()
			if smtp.has_extn('STARTTLS'):
				smtp.starttls()
				smtp.ehlo()
				smtp.login('ponchio', 'maldipancismo')

				smtp.sendmail('ponchio@isti.cnr.it', [email], msg.as_string())
		except:
			logging.debug("Failed to send email.." % str(email))
		finally:
			smtp.quit()



	def send4science(self, media, token, subject, text):

		data = json.dumps({ "subject": subject, "body": text, "recipients": [{ "id": media["username"] }] })

		req = urllib2.Request('https://socialnetworking1.d4science.org/social-networking-library-ws/rest/2/messages/write-message?gcube-token=' + token)
		req.add_header('Content-Type', 'application/json')
		req.add_header('gcube-token',  token)

		try:
			response = urllib2.urlopen(req, data)

		except urllib2.HTTPError as err:
			logging.error("4Science error:" + str(err.code))

##### PROCESS 3d ######
	def autodetect(self, filename):
		with open(filename) as file:
			header = file.read(4)
			if header == "ply\n":
				return "ply"
			if header[0] == '#' or header[0] == 'm' or header[0] == 'v':
				return "obj"
				

		return ''

	def peekMtl(self, filename):
		if not os.path.exists(filename):
			return (False, "Could not find .mtl file: " + filename)

		with open(filename) as file:
			line = next(file)
			first = line.split()[0]
			if first in ['map_Kd', 'map_Ks', 'map_Ns', 'map_d', 'map_bump', 'bump', 'disp', 'decal']:
				image = line.split()[-1]
				if not os.path.exists(image):
					return (False, "Could not find texture file: " + image)
		return (True, "")

#assumes filename haas no path, and the current dir is the same as the file.


	def peekObj(self, filename):
		if not os.path.exists(filename):
			return (False, "Could not find file: " + filename)
	
		with open(filename) as file:
			for x in range(100):
				line = next(file, None)
				if line is None:
					return (True, "")
				if not line.startswith("mtllib"):
					continue
				mtl = line[7:].rstrip().strip('"')
				(success, error) =  self.peekMtl(mtl)
				if not success:
					return (success, error)

		return (True, "")

	def peekPly(self, filename):
		if not os.path.exists(filename):
			return (False, "Could not find file: " + filename)
		with open(filename) as file:
			line = next(file)
			if not line.startswith('ply'):
				return (False, filename + "is not a .ply file")
			for x in range(100):
				line = next(file)
				if line == "" or line == "\n" or line.startswith('end_header'):
					break
				if line.lower().startswith("comment texturefile "):
					image = line[20:].strip()
					logging.debug("Looking for: " + image)
					if not os.path.exists(image):
						return (False, "Could not find texture file: " + image)

		return (True, "")
		

	def process3d(self, media, path):


		plys = []
		for file in media["files"]:

			if file['ext'] == 'nxs' or file['ext'] == 'nxz':
#				just rename it to the final dir
				try:
					subprocess.check_output(["cp", file['filename'], path + media["label"] + ".nxz"], stderr=subprocess.STDOUT)
					return None
				except subprocess.CalledProcessError as e:
					logging.error(e.output)
					return e.output

			#check for textures
			if file['ext'] == 'obj':
				(success, error1) = self.peekObj(file['filename'])
				if not success:
					return error1

			if file['ext'] == 'ply':
				(success, error1) = self.peekPly(file['filename'])
				if not success:
					return error1


			if file["ext"] == "ply" or file["ext"] == "obj":
				plys.append(file["filename"])

		args = [nexus, '-o', 'test.nxs'] + plys
		error = None
		
#nxsbuilder
		try:
			output = subprocess.check_output(args, stderr=subprocess.STDOUT)
		#subprocess.check_output("dir /f",shell=True,stderr=subprocess.STDOUT)

		except OSError as e:
			logging.error(str(e))
			return str(e)

		except subprocess.CalledProcessError as e:
			logging.error(e.output)
			error = e.output.split('Fatal error:')
			error = error[1] if len(error) == 2 else 'Unknown error. Contact us.'
			return error;

#compression
		try:
			filesize = os.stat('test.nxs').st_size
			if filesize > 300000:
				output = subprocess.check_output([nxsedit, "-z", "test.nxs", "-o", "test.nxz"],stderr=subprocess.STDOUT)
			else:
				subprocess.check_output(["cp", "test.nxs", "test.nxz"])

		except OSError as e:
			logging.error(str(e))
			return str(e)

		except subprocess.CalledProcessError as e:
			logging.error(e.output)
			error = e.output.split('Fatal error:')
			error = error[1] if len(error) == 2 else 'Unknown error. Contact us.'
			return error

#cleanup
		try:
			subprocess.check_output(["mv", "test.nxz", path + media["label"] + ".nxz"], stderr=subprocess.STDOUT)
			subprocess.check_output(["rm", "test.nxs"], stderr=subprocess.STDOUT)
			subprocess.check_output(['rm -f cache_stream* cache_plyvertex* cache_tree*'], shell=True, stderr=subprocess.STDOUT)
		except subprocess.CalledProcessError as e:
			logging.error(e.output)
			return e.output


#move to view directory

		return None



##### PROCESS RTI ######

	def processRti(self, media, path):

		error = None
		first = True
		for file in media["files"]:
			if file["format"] != "rti":
				continue

			outdir = path + file["label"]
			try:
#process rti
				if file["ext"] == 'json':
					output = subprocess.check_output(['mkdir', '-p', outdir])
					output = subprocess.check_output(['cp info.json plane_*.jpg ' + outdir], shell=True)
				else:
					logging.debug("RTI process %s %s %s" % (relight, file["filename"], outdir))
					output = subprocess.check_output([relight, file["filename"], outdir], stderr=subprocess.STDOUT)

				output = subprocess.call([deepzoom, outdir]);

			except subprocess.CalledProcessError as e:
				logging.error(e.output)
				error = e.output
				break

#get size
			try:
				logging.debug("Loading " + outdir + "/info.json")
				with open(outdir + '/info.json') as f:
					data = json.load(f)

			except IOError as e:
				error = "Could not process the file. Sorry, ask for support."
				logging.error(error)
				break
			except ValueError as e:
				error = str(e)
				logging.error(error)
				break

			self.setSize(media["id"], data['width'], data['height'])

#create thumb
#			if first:
#				try:
#					output = subprocess.call(['vipsthumbnail', outdir + "/plane_0.jpg", '-o', 
#						data_path + media["path"] + file["label"] + '.jpg[Q=92,optimize_coding,strip]', '--delete', '--size', '300x200']);
#					self.setThumbnail(media["id"], file["label"] + '.jpg')
#				except subprocess.CalledProcessError as e:
#					logging.error(e.output)
#					error = "Could not create a thumbnail."
#				break;
#				first = False

		return error






##### PROCESS IMG ######

	def processImg(self, media, path):

		logging.debug("processImg")

		error = None
		first = True
		for file in media["files"]:
			if file["format"] != "img":
				continue

			outdir = path + file["label"]
			basename = os.path.splitext(file["filename"])[0]

# get image size
# careful with tif.
			try:
				output = subprocess.check_output(["identify", "-format", "%w %h\n", file["filename"]])
				output = output.split('\n')[0]
				w, h = output.split(' ')
				self.setSize(media["id"], w, h)
			except:
				logging.debug("failed identify");
				error = "Failed processing image: " + file["filename"]
				break;

#create thumb
			try:
				output = subprocess.call(['vipsthumbnail', file["filename"], '-o', 
					outdir + '.jpg[Q=92,optimize_coding,strip]', '--delete', '--size', '300x200']);
#				output = subprocess.call(['convert', '-resize', '300x200', '-strip', '-quality', '92', file["filename"],  
#					outdir + '.jpg']);
				
				if first:
#					subprocess.call(["cp", path + thumbbasename + ".jpg", outdir + ".jpg"]);
					self.setThumbnail(media["id"], file["label"] + '.jpg')
			except subprocess.CalledProcessError as e:
				logging.error(e.output)
				error = "Could not create a thumbnail."
				break;
			first = False

#create deepzoom
			try:
				output = subprocess.call(['rm', '-rf', outdir + "_files"], shell=False)
				output = subprocess.call(['vips', 'dzsave', file['filename'], outdir,
					'--layout', 'dz', '--tile-size', '256', '--overlap', '0', '--depth', onetile, '--suffix', '.jpg[Q=95]']);

			except subprocess.CalledProcessError as e:
				logging.debug("there was a problem")
				logging.error(e.output)
				error = "Could not process the image."
				break;
			except:
				error = "Unexpected error:", sys.exc_info()[0]


		return error


### DOWNLOAD JOB

	def get_filename_from_cd(self, cd):
		if not cd:
			return None
		filename = re.findall('filename=(.+)', cd)
		if len(filename) == 0:
			return None
		return filename[0]



	def downloadJob(self, media):

		logging.debug("Downloading job %s" % media['id']);
		global upload_path
		urls = media['url']
		if urls is None:
			self.setStatus(media['id'], 'failed', 'No url was given.')
			return


		for url in urls.split(' '):
			try:
				path = upload_path + media["path"]
				logging.debug("path: " + path)
				try:
					output = subprocess.call(["mkdir", "-p", path])

				except OSError as e:
					if e.errno != errno.EEXIST:
						raise   
					pass


				filename = url.rsplit('/', 1)[-1]

				r = requests.get(url, allow_redirects=True)
				if r.status_code == 404:
					raise ValueError("404 file not found")
				cd = self.get_filename_from_cd(r.headers.get('content-disposition'))
				if cd is not None:
					filename = cd
				open(path + filename, 'wb').write(r.content)

				name, extension = os.path.splitext(filename)

				formats = { 'jpg':'img', 'jpeg':'img', 'png':'img', 'gif':'img', 'tif':'img', 'tiff':'img', 
					'ply':'3d', 'obj':'3d', 'mtl':'other', 'nxs': '3d', 'nxz': '3d',
					'rti':'rti', 'ptm':'rti', 'json':'rti' }

				if extension != '':
					extension = extension.lower()
					extension = extension[1:]
				
				#autodetext if no extension and if found rename the file appropriately
				if extension not in formats:
					extension = self.autodetect(path + filename)
					if extension != '':
						newfilename = filename + '.' + extension
						output = subprocess.call(["mv", path + filename, path + newfilename])
						filename = newfilename
						


				if extension in formats:
					format = formats[extension]
				else:
					format = 'unknown'

				query = "insert into files (media, format, ext, size, original, filename, label) values \
					(%s, %s, %s, %s, %s, %s, %s );"
				self.cur.execute(query, [media['id'], format, extension, 0, filename, filename, filename])
				self.con.commit()

			except  Exception as e:

				logging.error("Failed downloading url {}".format(url))
				self.setStatus(media['id'], 'failed', 'Failed downloading url: ' + str(e))
				return

		self.setStatus(media["id"], 'on queue', None)



##### PROCESS JOB ######

	def processJob(self, media):
		global upload_path, data_path

		id = media["id"]
		path = data_path + media["path"]
		label = media["label"]

		media_type = media["media_type"]
		logging.debug("Processing job %s: %s %s" % (id, label, media_type))

		try:

			output = subprocess.call(["mkdir", "-p", path])
			os.chdir(upload_path + media["path"])
		
		except subprocess.CalledProcessError as e:
			logging.error("error %s" % (e.output))
			error = "Failed to create the output folder."

		error = None
		if media_type == '3d':
			error = self.process3d(media, path)
		elif media_type == 'rti':
			error = self.processRti(media, path)
		elif media_type == 'img' or media_type == 'album':
			error = self.processImg(media, path)

		self.cur.execute("SELECT * FROM users WHERE id = %s", [media["userid"]])
		user = self.cur.fetchone()
		
		self.cur.execute("SELECT * FROM identities WHERE provider = 'd4science' AND userid = %s", [user["id"]])
		user["d4science"] = self.cur.fetchone()


		if error != None:
			logging.debug("error processing: %s" %(error))
			media["error"] = error;
			self.setStatus(id, 'failed', error)
			self.sendMsgFail(media, user)
			return

		try:
			#make sure www-data can access created files.
			output = subprocess.call(["chmod", "-R", "g+w", path])
			
		except subprocess.CalledProcessError as e:
			logging.error(e.output)
			error = "Permissions problems. Contact us."

		if error != None:
			media["error"] = error;
			self.setStatus(id, 'failed', error)
			self.sendMsgFail(media, user)
			return
		
		self.setStatus(id, 'ready', None)
		self.sendMsgSuccess(media, user)

		return

	def removeJob(self, media):
		global upload_path, data_path

		id = media["id"]


		logging.debug("Removing job %s." % id)

		try:
			path = data_path + media["path"]
			if os.path.exists(path):
				shutil.rmtree(path)
	
			path = upload_path + media["path"]
			if os.path.exists(path):
				shutil.rmtree(path)
			
			self.cur.execute("delete from files where media = %s", [id])
			self.cur.execute("delete from media where id = %s", [id])
			self.con.commit();
			
		except Exception as e:
			logging.error("Could not remove  job {}. {}".format(id, e))


	def run(self):
		global quit
		logging.basicConfig(filename=log_path,level=logging.DEBUG)

		os.chdir(upload_path)
		os.umask(0o002)

		try:
			print("Line 631: Fill database password and commend this line!");
			self.con = psycopg2.connect(host="localhost", database='vms', user='ariadne', password='')
			self.cur = self.con.cursor(cursor_factory=psycopg2.extras.RealDictCursor)

			logging.debug('Connected to DB')

			while quit == False:
				try:
					sql = \
"SELECT m.id, m.status, m.label, m.title, m.path, m.media_type, m.url, m.set, m.thumbnail, m.secret, m.userid, m.expire < now() as expired, u.name, u.username, u.email \
FROM media m \
LEFT OUTER JOIN users u on u.id = m.userid  \
WHERE status in ('on queue', 'processing', 'download', 'remove') OR (expire is not null and expire < now()) ORDER BY creation"

					self.cur.execute(sql)
					job = self.cur.fetchone()
					self.con.commit()

					if job is not None:
						logging.debug(str(job['id']) + ' ' + job['status'])
						if job['expired'] or job['status'] == 'remove':
							self.removeJob(job)

						elif job['status'] == 'download':
							self.downloadJob(job)

						elif job['status'] == 'on queue' or job['status'] == 'processing':

							self.cur.execute("SELECT * FROM files WHERE media = %(id)s", job)
							job['files'] = self.cur.fetchall()

							self.cur.execute("SELECT * FROM identities WHERE userid = %(userid)s", job)
							job['identity'] = self.cur.fetchall()

							logging.debug("processjob");
							self.processJob(job)


				except psycopg2.ProgrammingError as e:
					logging.error('Error 10: %s' % e)
				except NameError as e:
					logging.error('Error 11: %s' % e)
				except Exception as e: 
					logging.exception(e)
					logging.error('Error 12: %s' % sys.exc_info()[0])
					exit()
#					self.con.rollback()

#				self.con.close()
#				exit()

				time.sleep(2)

			logging.debug('Exiting')

		except psycopg2.DatabaseError as e:
			logging.error('Error 4 %s' % e)    
			sys.exit(1)

		finally:
			if self.con:
				self.con.close()


if __name__ == "__main__":

	daemon = Ariadne('/tmp/ariadne-vms.pid')
#	daemon.run()

	if len(sys.argv) == 2:
		if 'start' == sys.argv[1]:
			daemon.start()
		elif 'stop' == sys.argv[1]:
			daemon.stop()
		elif 'restart' == sys.argv[1]:
			daemon.restart()
		else:
			print("Unknown command")
			sys.exit(2)
		sys.exit(0)
	else:
		print("usage: %s start|stop|restart" % sys.argv[0])
		sys.exit(2)


