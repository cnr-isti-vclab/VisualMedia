
SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET client_min_messages = warning;

SET default_tablespace = '';

SET default_with_oids = false;
SET search_path TO public;

-- Table: object
CREATE TABLE public.media
(
  id serial NOT NULL,
  label text NOT NULL, -- Name used in urls to identify media.
  media_type text, -- Actually should be an enum. TODO: change it.
  set integer DEFAULT 0,
  title text, -- Title of the media
  description text, -- Description in plain ascii.
  collection text, -- Collection, dataset, just a string.
  owner text,      --copyright owner
  url text, -- Url to resource.
  creation timestamp without time zone,
  userid integer,
  processed integer DEFAULT 0, --if processed (in case we are reprocessing)
  status text, --thi is the status of the processing uploading, 'on queue', processing, ready, failed
  todo text, -- operation to be performed on the media, json witth operation and parameters
  variants text, -- json with info about the variants of the file (size etc)
  error text, --refers to the last processing operation
  publish integer DEFAULT 0,
  path text,  -- path where the files are stored, usually just the label
  thumbnail text,
  width integer, -- metadata of the processed files
  height integer,
  mtri integer,
  mm integer,   -- size of a pixel or a unit
  size integer, -- Size on disk in Kb.
  secret text, -- backward compatibility.
  options text, -- JSON options
  picked integer,
  words tsvector,
  expire timestamp without time zone
)
WITH ( OIDS=FALSE );
ALTER TABLE media OWNER TO vms;

-- Table: media
CREATE TABLE files
(
  id serial NOT NULL,
  label text, -- used as a savefile name.
  media integer NOT NULL,
  status text, -- status of the file, such as uploading, processing, ready, failed
  format text, -- img 3d rti etc.
  ext text, --extension
  description text, -- used by img collections
  options text, --json options such as trackball, etc.
  ordering integer, -- in case of sets, books
  width integer,
  height integer,
  mtri integer,
  size integer, -- Size on disk in Kb.
  filename text, --local filename
  original text, --remote filename, used for checking
  processing_start timestamp without time zone,
  processing_end timestamp without time zone
)
WITH ( OIDS=FALSE );
ALTER TABLE files OWNER TO vms;



-- Table: collections
CREATE TABLE collections
(
  id serial NOT NULL,
  userid integer,
  label text,
  title text,
  description text,
  publish integer DEFAULT 0,
  category text,

  CONSTRAINT collections_id_key PRIMARY KEY (id),
  CONSTRAINT label_collections_u UNIQUE (label)
)
WITH ( OIDS=FALSE );
ALTER TABLE collections OWNER TO vms;



-- Table: collections_media
CREATE TABLE collections_media
(
  collection integer NOT NULL,
  media integer NOT NULL,
  CONSTRAINT collections_media_key PRIMARY KEY (collection, media)
)
WITH ( OIDS=FALSE );
ALTER TABLE collections_media OWNER TO vms;



-- Table: users
CREATE TABLE users
(
  id serial NOT NULL,
  username text,
  role text DEFAULT 'user',
  email text,
  name text,
  institution text,
  created date,
  validate text, --used for validating
  sendemail integer DEFAULT 0,
  CONSTRAINT users_id_pk PRIMARY KEY (id),
  CONSTRAINT username_users_u UNIQUE (username),
  CONSTRAINT email_users_u UNIQUE (email),

  CONSTRAINT validate_users_u UNIQUE (validate)

)
WITH ( OIDS=FALSE );
ALTER TABLE users OWNER TO vms;
GRANT ALL ON TABLE users TO vms;



-- Table: identities
CREATE TABLE identities
(
  userid integer, -- in table users
  provider text, -- google, faceboook, passwordless

  uid text,
  token text,    --stored in session
  access_token text,
  refresh_token text,
  picture_url text,

  mergecode text, -- use this to merge two user
  mergeid integer, -- use store here which user
  CONSTRAINT identities_token_pk PRIMARY KEY (provider, uid),
  CONSTRAINT token_identities_u UNIQUE (token)
)
WITH ( OIDS=FALSE );
ALTER TABLE identities OWNER TO vms;
GRANT ALL ON TABLE identities TO vms;
