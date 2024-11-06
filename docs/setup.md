in ariadne_upload e in data!

sudo chmod g+s ariadne_upload
sudo chmod g+w ariadne_upload
sudo chown -R www-data:ponchio ariadne_upload


sudo chmod g+s data
sudo chmod g+w data
sudo chown -R www-data:ponchio data


sudo usermod -a -G www-data vcg

libvips-tools
relight
and deepzoom.sh

ricordarsi che in 3dhop ci vuole il flag 

hp_value upload_max_filesize
Php_value post_max_size
