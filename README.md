# VisualMediaService

Visual media service is a simple repository for uploading 3D and 2D (RTI) models and generating streamable 3D reparesentations that can be shown efficiently on the web using [3DHop](https://3dhop.net) and [OpenLime](https://github.com/cnr-isti-vclab/openlime)

This server is currently serving the [Ariadne Visual Media Service](https://visual.ariadne-infrastructure.eu/) to provide easy publication and presentation on the web of complex visual media assets. 
It is an automatic service that allows to upload visual media files on an server and to transform them into an efficient web format, making them ready for web-based visualization.
The Visual Media Service was born as part of FP7 EU-INFRA Ariadne project.

## Supported Formats
The repository supports common 3D formats for meshes and point clouds, such as .PLY .OBJ .OFF .TSP
Relightable images (RTI) can be imported using the old .ptm and .rti format or the more recent [Relight](https://github.com/cnr-isti-vclab/relight) format.

## Authentication
Authentication is based on OAuth protocol, alternatively a simple email-based passwprdless authentication can be used. 

## Storage

## Configuration

Copy the `smtp_template.env` file to `smtp.env` and edit the SMTP settings to your needs.

## Installation with docker

To try in your machine the Visual Media Service, you just need to have [docker](https://docs.docker.com/) installed.

It's better to run docker in userspace, in linux:
```
sudo usermod -aG docker $USER
```

Configure the `smtp.env` file. Then, from the root of the repository, just run:

```
docker compose up
```

or, in detached mode:

```
docker compose up --detach
```

And you can access to the service from a browser, at http://localhost.

If you need to change the port, you can edit the [docker-compose.yml](docker-compose.yml) file and change the port mapping in the `php` service section.

To stop the service, you can run:

```
docker compose down
```

## Installation without docker

See Docker file for scripts:

Setup apache (virtual host, php.ini (see scripts/php_settings.sh)
Setup posgres
Setup codeigniter
Setup python
Move env file into www/application/config/


