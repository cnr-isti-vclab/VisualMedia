<div class="row">
<div class="offset-md-2 col-md-8">

<h2>High-resolution Images</h2> 

<p>Most of the images produced nowadays are very high-resolution. High-resolution 
images are now a commodity resource, with the impressive evolution of digital 
photography (just to mention a single example, a recent off-the-shelf smartphone 
provides a 41 Mpixel camera). 
When high- or huge-resolution images are available, the visualization on the web 
can be difficult, due to the amount of data that have to be transmitted before the 
web browser would be able to present visually something. This is because the browser 
has to receive the entire file before visualizing it. 
Another important and critical issue could be the necessity to protect the data, 
avoiding sending the full resolution image as a single file to a remote host.</p>

<p>This service converts a high-resolution image (uploaded using standard image formats) 
into a multi-resolution version, enabling progressive transmission and rendering
 it by means of a web visualization page.</p>

<p>We offer a service for converting a high-resolution image (uploaded using standard 
image formats) into a multiresolution format, enabling progressive transmission 
and visualization by means of a web-enabled image-browser.</p>

<p>Our service is based on <a href="http://en.wikipedia.org/wiki/WebGL">WebGL</a>, 
<a href="http://spidergl.org">SpiderGL</a> and <a href="http://3dhop.net">3DHOP 
(3D Heritage On-Line Presenter)</a>, where the latter are platforms designed and 
implemented by <a href="http://www.isti.cnr.it">CNR-ISTI</a>. The main advantage of this service is that the handling 
of the image doesn’t need the installation of a local server.</p>

<p>The service is totally automatic: you have first to fill a simple web form with 
some info on the image you are willing to convert, and then upload the image. 
Our service will convert your model, will store it on a web server and will 
return you a URL you may use to access the data. </p>

<p>According to user needs, we can also return a .zip file containing all the data 
produced, to allow the user to store the data on his preferred web server.</p>

<p>Please note: the server accepts in input images encoded with the .png, .jpg, .tif formats.</p>

<p></p>

<h3>Some more info:</h3>

<p>The service transforms each image in a web-compliant format. Following the approach 
used by <a href="http://maps.google.com">Google Maps</a>, the high-resolution image will be 
regularly divided in chunks and a hierarchy of images at different resolution is 
produced from these chunks. Each image of this sequence is splitting in square 
tiles of fixed size (usually 256 pixels) to permit the data management at high 
granularity. The client in the browser “composes” on the fly the portion of the 
image selected by the user using the tiles more suitable according to the size 
of the portion under view. This approach is a simple multi-resolution one that 
has been demonstrated to be very efficient to visualize this type of data. 
The same approach can be employed to visualize high- or huge- resolution images. 
The image visualization webpage we produce allows to navigate the model in a WebGL 
frame (no plugin have to be installed, it will work natively on all the main 
browsers supporting WebGL, with the current exception of smartphones and tablets). </p>

<h3>Related publications:</h3>

<p>M. Corsini, M. Dellepiane, U. Dercks, F. Ponchio, M. Callieri, D. Keultjes, A. Marinello, R. Sigismondi, R. Scopigno, G. Wolf,<br/>
<i>CENOBIUM - Putting together the Romanesque Cloister Capitals of the Mediterranean Region, </i><br/>
BAR International Series , Volume 2118, page 189-194 - 2010</p>

</div>
<div class="col-md-2"><a href="/upload/img" class="btn btn-info float-right"><i class="fas fa-upload"></i> Upload</a>
</div>
</div>
