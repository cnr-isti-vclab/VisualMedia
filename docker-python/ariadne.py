#!/usr/bin/env python

import sys, time, psycopg2, logging, signal, os, subprocess, re, json
import psycopg2.extras
import pprint
#from daemon import Daemon
from datetime import datetime

import json
import urllib
import urllib.request as urllib2
import requests
import smtplib
import shutil 

from email.mime.text import MIMEText

#import functions from processing.py
import processing

quit = False

#TODO read 

host = os.environ['WEBSITE_HOST']
admin_user  = os.environ['SMTP_USER']
admin_pass = os.environ['SMTP_PASSWORD']
admin_email = os.environ['ADMIN_EMAIL']

postgres_user = os.environ['POSTGRES_USER']
postgres_pass = os.environ['POSTGRES_PASSWORD']
postgres_host = os.environ['POSTGRES_HOST']
postgres_db   = os.environ['POSTGRES_DB']

upload_path = '/data/vms_upload/'
data_path   = '/data/vms_data/'
log_path    = '/data/ariadne.log'

relight  = '/home/ubuntu/relight/usr/bin/relight-cli'
deepzoom = '/home/ubuntu/relight/build_deepzoom.sh'
nexus    = '/home/ubuntu/nexus/usr/bin/nxsbuild'
nxsedit  = '/home/ubuntu/nexus/usr/bin/nxsedit'

def sigterm_handler(_signo, _stack_frame):
	global quit
	quit = True


signal.signal(signal.SIGTERM, sigterm_handler)

#class Ariadne(Daemon):
class Ariadne():
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
		media['name'] = user['name']
		
		text = """Dear %(name)s,
   unfortunately the media (%(title)s) you uploaded could not be processed.

%(error)s

Most probably the media was in a format we do not support, please check under

    %(host)s/help#supported

for the details of the supported media.

You can still access your media info (and delete it) from:

    %(host)s/media/%(label)s

If you think the media was the correct format, or for any other reason, please
contact us at

%(admin_email)s

Thank you for using our service, and sorry for the trouble

Visual Media Service.""" % media

		self.sendMsg(media, user, '[AMS] Bummer! %s processing failed.' % media["title"], text)




	def sendMsgSuccess(self, media, user):
		media['name'] = user['name']

		text = """Dear %(name)s,
   the media (%(title)s) you uploaded has been processed successfully.

You can view the result privately here:

    %(host)s/%(media_type)s/%(secret)s

and if you allowed it publicly here:

    %(host)s/%(media_type)s/%(label)s

you can edit the related information, download the result, or delete it here:

    %(host)s/media/%(label)s

Thank you for using our service, 

Visual Media Service.

P.S. If you need to contact us write to: %(admin_email)s""" %  media

		self.sendMsg(media, user, '[AMS] %s is ready.' % media["title"], text)


	def sendMsg(self, media, user, subject, text):
		if user["sendemail"] != 1:
			return

		if user["d4science"]:
			self.send4science(media, user["d4science"]["token"], subject, text)
		else:
			self.sendEmail(media["email"], subject, text)
		self.sendEmail(admin_email, subject, text)


	def sendEmail(self, email, subject, text):
		logging.debug("Sending mail to %s" % email)
		# Create a text/plain message
		msg = MIMEText(text)

		# me == the sender's email address
		# you == the recipient's email address
		msg['Subject'] = subject
		msg['From'] = admin_email
		msg['To'] = email

		smtp = smtplib.SMTP('smtp-out.isti.cnr.it', 587)
		try:

			smtp.set_debuglevel(0)
			smtp.ehlo()
			if smtp.has_extn('STARTTLS'):
				smtp.starttls()
				smtp.ehlo()
				smtp.login(admin_user, admin_pass)

				smtp.sendmail(admin_email, [email], msg.as_string())
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
		logging.debug("peekPly: " + filename)
		if not os.path.exists(filename):
			return (False, "Could not find file: " + filename)
		with open(filename, "rb") as file:
			line = file.readline().decode("ascii").strip()
			if not line:
				return (False, filename + " is empty")

			#line = next(file)
			if not line.startswith('ply'):
				return (False, filename + "is not a .ply file")
			for x in range(100):
				line = file.readline().decode("ascii").strip()
				if line == "" or line == "\n" or line.startswith('end_header'):
					break
				if line.lower().startswith("comment texturefile "):
					image = line[20:].strip()
					logging.debug("Looking for: " + image)
					if not os.path.exists(image):
						return (False, "Could not find texture file: " + image)

		return (True, "")
		

	def process3d(self, media, output_nxz):
		logging.debug("process3d to " + output_nxz)

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
		
		logging.debug("nxsbuild: " + str(plys))

#nxsbuilder
		try:
			output = subprocess.check_output(args, text=True, stderr=subprocess.STDOUT)
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
				output = subprocess.check_output([nxsedit, "-z", "test.nxs", "-o", "test.nxz"], text=True, stderr=subprocess.STDOUT)
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
			subprocess.check_output(["mv", "test.nxz", output_nxz], text=True, stderr=subprocess.STDOUT)
			subprocess.check_output(["rm", "test.nxs"], text=True, stderr=subprocess.STDOUT)
			subprocess.check_output(['rm -f cache_stream* cache_plyvertex* cache_tree*'], shell=True, text=True, stderr=subprocess.STDOUT)
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
					output = subprocess.check_output(['mkdir', '-p', outdir], text=True, )
					output = subprocess.check_output(['cp info.json plane_*.jpg ' + outdir], text=True, shell=True)
				else:
					logging.debug("RTI process %s %s %s" % (relight, file["filename"], outdir))
					output = subprocess.check_output([relight, file["filename"], outdir], text=True, stderr=subprocess.STDOUT)

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
				output = subprocess.check_output(["vipsheader", file["filename"]], text=True)
				output = output.split('\n')[0]
				# Example: "file.jpg: 1920x1080 uchar, 3 bands, srgb, jpegload"
				dims = output.split(':')[1].split('x')
				w = dims[0].strip()
				h = dims[1].split(' ')[0].strip()
				self.setSize(media["id"], w, h)
			except Exception as e:
				logging.debug("Failed vipsheader: {e}");
				error = "Failed to identify image: " + file["filename"]
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
					'--layout', 'dz', '--tile-size', '256', '--overlap', '0', '--depth', 'onetile', '--suffix', '.jpg[Q=95]']);

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
	def getUser(self, userid):
		self.cur.execute("SELECT * FROM users WHERE id = %s", [userid])
		user = self.cur.fetchone()
		if user['name'] is None:
			user['name'] = user['username']
		if user['name'] is None:
			user['name'] = user['email']

		if user is None:
			logging.error("User with id %s not found." % userid)
			return None
		return user

	def fail(self, media, user, error):
		logging.error("Failed processing job %s: %s" % (media["id"], error))
		media['error'] = error
		self.setStatus(media["id"], 'failed', error)
		self.sendMsgFail(media, user)
		return

	def processJob(self, media):
		global upload_path, data_path

		id = media["id"]
		path = data_path + media["path"]
		label = media["label"]

		media_type = media["media_type"]
		logging.debug("Processing job %s: %s %s" % (id, label, media_type))

		user = self.getUser(media["userid"])


		try:

			output = subprocess.call(["mkdir", "-p", path])
			os.chdir(upload_path + media["path"])
		
		except subprocess.CalledProcessError as e:
			self.fail(media, user, "Failed to create the output folder.")
			return

		error = None
		if media_type == '3d':
			output_nxz = path + media["label"] + ".nxz"
			error = self.process3d(media, output_nxz)
		elif media_type == 'rti':
			error = self.processRti(media, path)
		elif media_type == 'img' or media_type == 'album':
			error = self.processImg(media, path)

		
		self.cur.execute("SELECT * FROM identities WHERE provider = 'd4science' AND userid = %s", [user["id"]])
		user["d4science"] = self.cur.fetchone()


		if error != None:
			self.fail(media, user, error)
			return

		try:
			#make sure www-data can access created files.
			output = subprocess.call(["chmod", "-R", "g+w", path])
			
		except subprocess.CalledProcessError as e:
			logging.error(e.output)
			error = "Permissions problems. Contact us."

		if error != None:
			self.fail(media, user, error)
			return
		
		self.setStatus(id, 'ready', None)
		self.sendMsgSuccess(media, user)

		return
	
	def modifyJob(self, media):
		global upload_path, data_path

		logging.debug("Modifying job %s" % media["todo"])

		id = media["id"]
		user = self.getUser(media["userid"])
		
		label = media["label"]
		todo = json.loads(media["todo"])
		logging.debug("GATTO!" + media['todo']);
		#expecting todo to have, version (the parent version!), action and relative parameters.

		if todo is None or todo['parent'] is None or todo['action'] is None:
			self.fail(media, user, 'Invalid todo: ' + media["todo"])
			return	

		variants = json.loads(media["variants"])
		logging.debug(json.dumps(variants, indent=2))
		#if parent version is 0, the input files are in the upload path (the originals)
		#otherwise they are in the data_path/<version>
		
		#check version exists\
		if todo['parent'] == '0':
			logging.debug("Using upload path for version 0")
			input_dir = upload_path + media["path"]
		else:
			logging.debug("Using data path for version %s" % todo['parent'])
			input_dir = data_path + media["path"] + str(todo['parent']) + "/"

		if not os.path.isdir(input_dir):
			self.fail(media, user, "Parent version does not exists: " + input_dir)
			return
		#find a 3d file in the input dir (.obj or .ply)
		input_files = [f for f in os.listdir(input_dir) if os.path.isfile(os.path.join(input_dir, f))]
		if len(input_files) == 0:
			self.fail(media, user, "No files found in the input directory: " + input_dir)
			return
		
		input_file = None
		for file in input_files:
			if file.endswith('.obj') or file.endswith('.ply'):
				input_file = file
				break
		
		if input_file is None:
			self.fail(media, user, "No 3d file found in the input directory: " + input)
			return


		#find a new id for version
		new_version = max(item['version'] for item in variants) + 1
		#create a new dir  in upload path
		output_dir = data_path + media['path'] + str(new_version) + "/"

		try:
			result = subprocess.call(["mkdir", "-p", output_dir])
			os.chdir(output_dir)
		
		except subprocess.CalledProcessError as e:
			self.fail(media, user, "Failed to create the output folder:" + output_dir)
			
			return

		input = input_dir + input_file
		output = output_dir + input_file

#		todo['action'] = 'dummy'
		try:
			#depending on the action call the processing.py relative function
			if todo['action'] == 'simplify':
				processing.simplify(input, output, todo['triangles'])
			elif todo['action'] == 'remesh':
				processing.remesh(input, output, todo['size'])
			elif todo['action'] == 'closeholes':
				processing.close_holes(input, output)
			elif todo['action'] == 'dummy':
				processing.dummy_processing(input, output)
		except Exception as e:
			self.fail(media, user, "Failed processing job {}: {}".format(id, e))
			return

		#update the variants with the new version
		variants.append({
			'version': new_version,
			'path': media['path'] + str(new_version) + '/',
			'parent': todo['parent'],
			'label': 'Version ' + str(new_version),
			'creation': str(datetime.now()),
		})
		update_variants = json.dumps(variants, indent=2)
		logging.debug("Updating variants: " + update_variants)
		self.cur.execute("UPDATE media SET variants = %s, todo = null WHERE id = %s", [update_variants, id])
		self.con.commit()
		#now we need to process the nexus, but we need to modify process3d to take the variants into account.
		os.chdir(output_dir)
		output_nxz = data_path + media["path"] + media["label"] + "_" + str(new_version) + ".nxz"

		try:
			error = self.process3d(media, output_nxz)
		except Exception as e:
			error = e.output
		
		if error is not None:
			self.fail(media, user, "Failed processing job {}: {}".format(id, error))
			return
		
		self.setStatus(id, 'ready', None)
		self.sendMsgSuccess(media, user)


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
		logging.basicConfig(filename=log_path, level=logging.DEBUG)

		os.chdir(upload_path)
		os.umask(0o002)

		try:
			# print("Line 631: Fill database password and commend this line!");
			self.con = psycopg2.connect(host=postgres_host, database=postgres_db, user=postgres_user, password=postgres_pass)
			self.cur = self.con.cursor(cursor_factory=psycopg2.extras.RealDictCursor)

			logging.debug('Connected to DB 2')

			while quit == False:
				try:
					sql = \
"SELECT m.id, m.status, m.todo, m.variants, m.label, m.title, m.path, m.media_type, m.url, m.set, m.thumbnail, m.secret, m.userid, m.expire < now() as expired, u.name, u.username, u.email \
FROM media m \
LEFT OUTER JOIN users u on u.id = m.userid  \
WHERE status in ('on queue', 'processing', 'modify', 'modifing', 'download', 'remove') OR (expire is not null and expire < now()) ORDER BY creation"

					self.cur.execute(sql)
					job = self.cur.fetchone()
					self.con.commit()

					if job is not None:
						logging.debug(str(job['id']) + ' ' + job['status'])
						job["host"] = host
						job["admin_email"] = admin_email
						if job['expired'] or job['status'] == 'remove':
							self.removeJob(job)

						elif job['status'] == 'download':
							self.downloadJob(job)

						elif job['status'] == 'on queue' or job['status'] == 'processing':
							self.setStatus(job['id'], 'processing', None)

							self.cur.execute("SELECT * FROM files WHERE media = %(id)s", job)
							job['files'] = self.cur.fetchall()

							self.cur.execute("SELECT * FROM identities WHERE userid = %(userid)s", job)
							job['identity'] = self.cur.fetchall()

							logging.debug("processjob");
							self.processJob(job)
						elif job['status'] == 'modify' or job['status'] == 'modifing':
							self.setStatus(job['id'], 'modifying', None)
							
							self.cur.execute("SELECT * FROM files WHERE media = %(id)s", job)
							job['files'] = self.cur.fetchall()

							self.cur.execute("SELECT * FROM identities WHERE userid = %(userid)s", job)
							job['identity'] = self.cur.fetchall()

							logging.debug("modifyJob");
							self.modifyJob(job)


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

	#daemon = Ariadne('/tmp/ariadne-vms.pid')
	daemon = Ariadne()
	daemon.run()

	# if len(sys.argv) == 2:
	# 	if 'start' == sys.argv[1]:
	# 		daemon.start()
	# 	elif 'stop' == sys.argv[1]:
	# 		daemon.stop()
	# 	elif 'restart' == sys.argv[1]:
	# 		daemon.restart()
	# 	else:
	# 		print("Unknown command")
	# 		sys.exit(2)
	# 	sys.exit(0)
	# else:
	# 	print("usage: %s start|stop|restart" % sys.argv[0])
	# 	sys.exit(2)


