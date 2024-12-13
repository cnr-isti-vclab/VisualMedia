<div class="row">
<div class="offset-md-2 col-md-8">

<h2>3D Models</h2>

<p>3D representations produced with 3D scanners or photogrammetry are extremely high-resolution 
and hard to visualize at interactive rate. This service produces a web page that supports 
interactive visualization of your data, after converting it into an efficient multiresolution encoding.</p>

<p>Very high-quality and high-resolution models can be easily produced with active 3D scanning 
(laser-based systems or system using structured light) or by adopting the recent photogrammetry 
approaches (production of 3D models from stream of images, also called Structure from Motion).</p>

<p>Presentation on the web of complex 3D models, i.e. models composed by millions of samples, 
is still a difficult task to achieve for many users. This is mainly for two reasons:  
it is hard to transmit/render such data in real time; and publishing 3D material on the 
web is still a task that only few developers are able to address. On the other hand, 
3D models cannot be confined to the single archaeologist’s archive, but should be shared 
with the community, to increase knowledge and stimulate further study.</p>

<p>We offer a service for converting a complex 3D model into a multiresolution format, 
progressively streamed on the web, supporting efficient visualization by means of a 
web-based visualization tool that runs under common web browsers. 
Our service is based on WebGL, <a href="http://spidergl.org">SpiderGL</a> and <a href="http://3dhop.net">3DHOP (3D Heritage On-Line Presenter)</a>, 
where the latter are platforms designed and implemented by <a href="http://www.isti.cnr.it">CNR-ISTI</a>. </p>

<p>The service is totally automatic: you have first to fill a simple web form with 
some info on the model you are willing to convert, and then upload the 3D model. 
Our service will convert your model, will store it on a web server and will return 
you a URL you may use to access the data. </p>

<p>According to user needs, we can also return a .zip file containing all the data produced,
 to allow the user to store the data on his preferred web server.</p>

<p>Please note: the server accepts in input 3D models encoded with the 
<a href="http://en.wikipedia.org/wiki/PLY_%28file_format%29">.ply format</a> up to 500Mb.  
Color information must be encoded as color per vertex or with a single texture. 
In the case of a model with texture, you will need to 
make a .zip file containing the model and the associated texture file.
If your model uses any other 3D format (or if you want to transfer color information 
from texture to vertices), please use the <a href="http://meshlab.sourceforge.net/">MeshLab</a> tool
to convert your data to the .ply format. If you need help, please <a href="/contacts">contact us</a>. </p>


<img src="/images/tut_on.jpg"/>


<h3>Some more info:</h3>

<p>Our efficient visualization tool is based on WebGL and a Javascript implementation 
of the <a href="http://vcg.isti.cnr.it/nexus/">Nexus multi-resolution framework</a>: 
the model to be visualized is pre-processed and converted in a collection of small fragments 
of a few thousands of triangles, at different resolutions. These fragments can 
be assembled together to approximate the original surface. Depending on the viewpoint, 
an optimal subset of fragments is selected to minimize the rendering error given 
a set amount of triangles. So, only the fragments effectively viewed by the users 
are required to be sent through the Web. This also supports some degree of 
protection of the data, since we never send to the remote user a complete and 
full-resolution model in a single stream of triangles, preventing the easy 
fraudulent copy of the high-resolution 3D file.</p>

<p>This approach is extremely efficient for a number of reasons:
It minimizes the CPU usage, as the assembling algorithm is quite simple. 
This is especially important since the client side is running in Javascript.
Using a collection of fragments supports naturally an out-of-core approach, 
which allows us to start rendering as soon as some data is coming, and chunk 
based data processing to minimize the effects of the network latency.
It is possible to optimize the rendering quality for a given amount of bandwidth.
Automatic pre-fetching is implemented to hide latency as much as possible.
There is no need for special server support: it just requires basic http protocol. 
In other words the browser itself handles both the streaming and rendering task.</p>

<p>Two recent important advancements have been reached and already implemented in the service:
a compressed format has been created, and the use of textured models is now possible.
Please check the related publication for more info.
</p>

<h3>Related publications:</h3>

<p>M. Potenziani, M. Callieri, M. Dellepiane, M. Corsini, F. Ponchio, R. Scopigno,<br/>
<i>3DHOP: 3D Heritage Online Presenter,</i><br/>
Computer & Graphics, Volume 52, page 129-141 - Nov 2015 </p>

<p>F. Ponchio, M. Dellepiane,<br/>
<i>Fast decompression for web-based view-dependent 3D rendering,</i><br/>
Web3D 2015. Proceedings of the 20th International Conference on 3D Web Technology, 2015</p>

<p>M. Potenziani, M. Corsini, M. Callieri, M. Di Benedetto, F. Ponchio, M. Dellepiane, R. Scopigno,<br/>
<i>An Advanced Solution for Publishing 3D Content on the Web,</i><br/>
Museums and the Web 2014 – 2014 </p>

<p>M. Callieri, C. Leoni, M. Dellepiane, R. Scopigno, <br/>
Artworks narrating a story: a modular framework for the integrated presentation 
of three-dimensional and textual contents,<br/> 
ACM WEB3D - 18th International Conference on 3D Web Technology, page 167-175 - June 2013</p>

<p>F. Larue, M. Di Benedetto, M. Dellepiane, R. Scopigno,<br/> 
From the Digitization of Cultural Artifacts to the Web Publishing of Digital 3D Collections: 
an Automatic Pipeline for Knowledge Sharing, <br/>
Journal of Multimedia, Volume 7, Number 2, page 132-144 - May 2012</p>


</div>
<div class="col-md-2"><a href="/upload/img" class="btn btn-info float-right"><i class="fas fa-upload"></i> Upload</a></div>
</div>
