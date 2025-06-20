<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the 'welcome' class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/



$route['default_controller'] = 'home';

//authentication

$route['passwordless']   = 'user/passwordless';
$route['login']          = 'user/login';
$route['home']           = 'user/login';
$route['user/listFiles'] = 'user/listFiles';
$route['logout']         = 'user/logout';
$route['login']          = 'user/login';
$route['profile']        = 'profile';
$route['profile/update'] = 'profile/updateProfile';
$route['profile/(:any)'] = 'profile/index/$1';


// main interface

$route['info/3d']    = 'home/info/3d';
$route['info/rti']   = 'home/info/rti';
$route['info/img']   = 'home/info/img';
$route['info/album'] = 'home/info/album';

$route['about']      = 'home/about';
$route['contacts']   = 'home/contacts';
$route['help']       = 'home/help';
$route['terms']      = 'home/terms';

$route['browse']        = 'home/browse';
$route['browse/(:any)'] = 'home/browse/$1'; //3d, rti, img, recent, category
$route['collections/(:any)'] = 'collectioncontroller/show/$1'; //same as before but only categories
$route['search']        = 'home/search';

$route['collections/(:any)'] = 'collectioncontroller/show/$1'; //label of the collection
$route['user/(:any)'] = 'collectioncontroller/user/$1'; //name of the user


$route['img/(:any)'  ] = 'mediacontroller/show/$1';
$route['rti/(:any)'  ] = 'mediacontroller/show/$1';
$route['3d/(:any)'   ] = 'mediacontroller/show/$1';
$route['album/(:any)'] = 'mediacontroller/show/$1';


$route['download/(:any)'] = 'admin/download/$1';
$route['upload'] = 'home/preupload';
$route['upload/(:any)'] = 'home/upload/$1';


$route['media/(:any)']             = 'mediacontroller/manage/$1';
$route['media/create']             = 'mediacontroller/create';
$route['media/delete/(:any)']      = 'mediacontroller/delete/$1';

$route['media/upload/file']        = 'mediacontroller/uploadFile';
$route['media/upload/file/(:any)'] = 'mediacontroller/uploadFile/$1';
$route['media/delete/file/(:any)'] = 'mediacontroller/deleteFile/$1';
$route['media/process/(:any)']     = 'mediacontroller/process/$1';

$route['media/update']             = 'mediacontroller/update';
$route['media/publish']            = 'mediacontroller/publish';
$route['media/unpublish']          = 'mediacontroller/unpublish';
$route['media/config/(:any)']      = 'mediacontroller/config/$1';
$route['media/update/config/(:any)']      = 'mediacontroller/updateConfig/$1';
$route['media/download/(:any)']    = 'mediacontroller/download/$1';
$route['media/status/(:any)']      = 'mediacontroller/status/$1';

$route['model/(:num)']             = 'modelcontroller/show/$1';

//user space

$route['collections/(:any)']                = 'collectioncontroller/show/$1';

$route['collection/create']                = 'collectioncontroller/create';
$route['collection/update']                = 'collectioncontroller/update';
$route['collection/manage/(:any)']         = 'collectioncontroller/manage/$1';
$route['collection/delete/(:any)']         = 'collectioncontroller/delete/$1';
$route['collection/(:num)/remove/(:num)']  = 'collectioncontroller/removeMedia/$1/$2';
$route['collection/(:num)/add/(:num)']     = 'collectioncontroller/addMedia/$1/$2';
$route['collection/batch/(:any)']          = 'collectioncontroller/batch/$1';
$route['collection/status/(:any)']         = 'collectioncontroller/status/$1';
$route['collection/publish']               = 'collectioncontroller/publish';
$route['collection/unpublish']               = 'collectioncontroller/unpublish';

$route['collection/uploadFile']            = 'collectioncontroller/uploadFile';
$route['collection/copyConfig/(:num)/(:num)'] = 'collectioncontroller/copyConfig/$1/$2';

$route['switchboard'] = 'mediacontroller/switchboard';
$route['regenerateSecretKeys/(:num)/(:num)'] = 'mediacontroller/regenerateSecretKeys/$1/$2';
// admin

//admin
//admin/jobs
//admin/usersr
//admin/collections
//admin pick
$route['admin/profile/(:any)'] = 'user/profile/$1';




$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

?>
