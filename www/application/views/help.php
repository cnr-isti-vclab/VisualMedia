<div class="row">
 <div class="col-md-8">
  <h2>Help!</h2>
 </div>
</div>
<div class="row">
 <div class="col-md-3">
  <ul>
   <li><a href="#motivation">Motivation</a></li>
   <li><a href="#supported">Supported media</a></li>
   <li><a href="#supported">How to use the service</a></li>
   <li><a href="#supported">Presentation features</a></li>
<!--     <ul>
       <li><a href="#images">Large images</a></li>
       <li><a href="#rti">RTI images</a></li>
       <li><a href="#3d">3D models</a></li>
     </ul> -->
    <li><a href="#faq">FAQ</a></li>
  </ul>
 </div>

 <div class="col-md-9">
  <h3><a name="motivation">Motivation</a></h3>
  <p>Visual Media Service has been created to provide an easy-to-use service for publishing 
advanced multimedia content on the web. The service was designed originally in the framework of the EU-INFRA
Ariadne project, but it has been further extended and it is open to all users. It is based on <a href="http://www.3dhop.net">3DHop</a>, a collection of tools and templates for the creation of multimedia interactive Web presentations
of digital cultural artifacts.</p>

  <h3><a name="supported">Supported media</a></h3>
  <p>The current version of Visual Media Service supports three types of media:</p>
  <ul>
    <li>3D models: the service is especially devoted to high resolution 3D models, since it 
uses a multi-resolution method to be able to handle complex geometries. For additional 
information please refer <a href="http://visual.ariadne-infrastructure.eu/info/3dmodels">here</a></li>

    <li>Re-lightable (RTI) images: re-lightable images are becoming more and more important 
for the acquisition and visualization of objects with small details (i.e. coins) or made of 
materials which are hard to acquire with other techniques. For additional information 
please refer <a href="http://visual.ariadne-infrastructure.eu/info/rti">here</a></li>

    <li>High resolution images: the service provide a solution also for the visualization of high 
resolution images, without the need of installing server-side services. For additional 
information please refer <a href="http://visual.ariadne-infrastructure.eu/info/images">here</a></li>
  </ul>

  <h3><a name="how">How to use the service</a></h3>
  <p>If you want to upload a new item, just click on the “upload” button on the home page of the 
service. You will be asked to provide some information about the item, and to upload the file (up to 500MB).
The service will process the file and send you an email when it will be ready. In the email you 
will find:</p>
  <ul>
    <li>A link to the generated page</li>
    <li>A link to the page describing the object, where you will be able to change the details, modify the presentation features
download the result, eventually remove it</li>
  </ul>
  
  <h3><a name="how">Presentation features</a></h3>
  <p>The new version of the service (released on February 2016) gives you the possibility to personalize the page.
  In the upload page, or in the generated page description, it will be possible to change:</p>
  <ul>
    <li>The navigation paradigm. It is now possible to choose among different trackballs: Turntable-pan, Sphere, Pan-tilt, Turntable. 
	These paradigms are the same used by 3DHop, please refer to the dedicated <a href="http://3dhop.net/examples.php?id=3" target="new">how to</a>
	in the 3DHop website.</li>
    <li>The additional tools. Some additional tools can be now added to the basic page: measure, picking, section, color on/off. "Measure" provides a a simple tools for measuring distances between points.
	"Picking" provides the three-dimensional coordinates of the picked point. "Section" allows to interactively sectioning the model. "Color on/off" allows to remove color information (if present). 
	A more detailed description of these tools can be found in the 3DHop <a href="http://3dhop.net/howto.php" target="new">how to</a> section.</li>
	<li>Background. It's possible to change the background image, or choose a color for it.</li>
	<li>Skin. The style of buttons can be chosen as well.</li>
  </ul>

  <h3><a name="faq">FAQ</a></h3>
  <h4>Can I view the models in my web pages?</h4>
  <p>Yes. You have two options</p>
  <ul>
   <li>Embed the viewer in an <code>&lt;iframe&gt;</code> in your page.</li>
   <li>Download the the zip package from the admin page of your models (you got the link in 
an email). The zip file contains the object (in the format needed for remote visualization), 
and the files of the simple web page which has been automatically generated.</li>
  </ul>

  <h4>Can I delete a model, or modify the title/description/url?</h4>
  <p>Indeed. You will receive a link to an admin page each time you upload something. You will be 
able to remove the item from there.</p>

  <h4>I don’t want other people to see my item, can I make it “private”?</h4>
  <p>Sure. It’s one of the options you can choose when you upload the object. In this case, the object 
won’t be visible on the list in the media service. If you want other people to see it, you can send 
them the link you can find at the beginning of the admin page of the item.</p>

  <h4>Can I change the presentation features after upload?</h4>
  <p>Yes, just go and modify the related description page. You will find a link to it in the email you received when the processing was successful</p>

  <h4>Can I upload a textured model?</h4>
  <p>Yes! now you can. You need to prepare a .zip file where you need to put the model and the texture file. Only 3D models with a single texture are supported for now. 
  The texture must be .jpg or .png format.</p>
  
  <h4>Can I upload a point clouds?</h4>
  <p>Of course, now they are fully supported. Again, the needed format is .ply</p>
  
  <h4>What does it mean that the model is "compressed"?</h4>
  <p>In order to save space, we developed a compression method that saves 90% of space w.r.t. to previous files, without any performance issue.
  There is a very small decrease in geometric detail, if this is an issue for please <a href="/contacts">contact us</a>. </p>

  <h4>I’d like to modify the web page that was generated by the service. Can I?</h4>
  <p>Only for the things that are part of the presentation features. If you want to make other changes (i.e. the parameters of the trackball), 
  you can download the zip file from the admin page 
of the item, and then use it as a starting point to improve it. The page is based on <a href="http://www.3dhop.net">3DHop</a>,
which has a set of wonderful functionalities that you can test!</p>

  <h4>So everybody will be able to download my model, right?</h4>
  <p> No, the zip file can be downloaded only from your private admin page, 
    and there’s no direct way to download the model from the visualization page.
    Remember though that, in order to make the media available, we have to send data
    to the user, and this stream can be intercepted and decoded. This is true for every
    online visualization system.</p>
<!--
  <h3><a name="images"></a>Large images</h3>

  <h3><a name="rti"></a>RTI images</h3>

  <h3><a name="3d"></a>3D models</h3> -->


 </div>
 
</div>

