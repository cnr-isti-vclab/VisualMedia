##Tables

media -> 3d (a single 3d or scene), rti (a single rti), img (collection of images)
upload specify what you want to upload!

#object
* id
* label (must be unique, used in URLS)
* media_type [3d, rti, img]
* set [true, false]
* title (what is shown to users)
* description (cimarkdown!)
* collection (this refers to the museum collection!)
* url (url of th resource
* owner (of the rights)
* publish
* creation
* ip

#media (type inferred from object.
* id
* object
* order
* format [corto, nexus, relight]
* width
* height
* mtri
* mvert
* size
* filename
* original [.ply, .obj .jpg etc.]
* status [processing, failed, success]
* processing_start, processing_end

#collection
* id
* userid
* label
* title
* description
* publish
* category

#collections_media
* collection
* media

