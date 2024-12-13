<div class="row">
<div class="offset-md-2 col-md-8">

<h2>RTI Images</h2>

<p>Relightable images (called Reflection Transformation Images, RTI, or Polynomial 
Texture Maps, PTM) are becoming an increasingly used technology to acquire 
a detailed documentation on small quasi-planar objects. They are particularly 
useful for documenting objects characterized by complex light reflection properties. 
The advantage of this representation is the possibility to change the light direction 
over the image in real time (i.e. at visualization time), and the availability of 
using enhanced visualization modes to better inspect fine details of the objects’ surface.</p>

<p>RTI technology (both acquisition and local rendering) is described in detail at 
<a href="http://culturalheritageimaging.org/Technologies/RTI/">http://culturalheritageimaging.org/Technologies/RTI/</a> </p>

<p>RTI images have been successfully applied in a number of applications, 
such as collections of coins, cuneiform tablets, inscriptions, carvings, 
bas-reliefs, paintings and jewellery.</p>

<p>Typically, this type of images is generated starting from a set of photographs 
acquired with a fixed camera under varying lighting conditions. RTI encodes the 
acquired data in a compact way, using view-dependent per-pixel reﬂectance functions, 
which allows the generation of the relighted image using any light direction in the 
hemisphere around the camera position.</p>

<p>More recently, technology for visualizing RTI images directly from any web browser
 has been introduced by <a href="http://www.isti.cnr.it">CNR-ISTI</a> (see an example of a web system presenting 
a <a href="http://vcg.isti.cnr.it/PalazzoBlu/">collection of coins at Palazzo Blu</a>).</p>

<p>We offer a service for converting any RTI image into a multiresolution format, 
progressively streamed on the web, supporting efficient visualization by means 
of a web-based visualization tool that runs under common web browsers. 
Our service is based on <a href="http://en.wikipedia.org/wiki/WebGL">WebGL</a>, 
<a href="http://spidergl.org">SpiderGL</a> and <a href="http://3dhop.net">3DHOP 
(3D Heritage On-Line Presenter)</a>, where the latter are platforms designed and implemented by CNR-ISTI. </p>

<p>The service is totally automatic: you have first to fill a simple web form 
with some info on the RTI image you are willing to convert, and then upload the 
RTI image. Our service will convert your image, will store it on a web server 
and will return you a URL you may use to access the data. </p>

<p>According to user needs, we can also return a .zip file containing all the 
data produced, to allow the user to store the data on his preferred web server.</p>

<p>Please note: the server accepts in input RTI images encoded with the .ptm 
(Polynomial Texture Maps) or .hsh (Hemispherical Harmonics) formats. 
Please refer to this <a href="http://culturalheritageimaging.org/What_We_Offer/Downloads/">page 
for hints on capturing and processing data.</a></p>

<p>
<img src="/images/rti_1.jpg" width="245">
<img src="/images/rti_2.jpg" width="245">
<img src="/images/rti_3.jpg" width="245"></p>

<h3>Some more info:</h3>

<p>The service transforms each image in a web-compliant format: similarly to Google maps, 
the high-resolution image will be regularly divided in chunks and a hierarchy of images 
at different resolution is produced from these chunks; then, a rendering webpage 
is created where it will be possible to navigate the model in a WebGL frame 
(no plugin have to be installed, it will work natively on all the main browsers 
supporting WebGL, with the current exception of smartphones and tablets). </p>

<p>The processing will be based on the components of the 
<a href="http://vcg.isti.cnr.it/~palma/dokuwiki/doku.php?id=research">WebRTI viewer</a>.</p>

<p>All the operations will be performed by scripted executables running on our server 
(WebRTIBuilder for the processing of the image).</p>

<h3>Related publications:</h3>

<p>G. Palma, M. Baldassarri, M. C. Favilla, R. Scopigno, <br/>
<i>Storytelling of a Coin Collection by Means of RTI Images: the Case of the Simoneschi Collection in Palazzo Blu,</i><br/> 
Museums and the Web 2014 </p>

<p>M. Mudge, C. Schroer, G. Earl, K. Martinez, H. Pagi, C. Toler-Franklin, S. Rusinkiewicz, G. Palma, M. Wachowiak, M. Ashley, N. Matthews, T. Noble, and M. Dellepiane. <br/>
<i>Principles and Practices of Robust, Photography based Digital Imaging Techniques for Museums.</i><br/>  
11th International Symposium on Virtual reality, Archaeology and Cultural Heritage VAST,  September 2010, pp. 111-137, Eurographics Association</p>

<p>G. Palma, M. Corsini, P. Cignoni, R. Scopigno, M. Mudge, <br/>
<i>Dynamic Shading Enhancement for Reflectance Transformation Imaging,</i><br/> 
ACM Journ. on Computers and Cultural heritag, Volume 3, Number 2 - set 2010</p>

</div>
<div class="col-md-2"><a href="/upload/img" class="btn btn-info float-right"><i class="fas fa-upload"></i> Upload</a></div>
</div>
