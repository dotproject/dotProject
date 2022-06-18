# dotProject

dotProject is an open source project management system written in PHP.

It originally started in 2001 by dotMarketing on SourceForge and has
been under the watchful eye of the current dotProject team since around December 2002.

## Installing/Upgrading

**NOTE** The `devel` branch is where all the development happens.  If you want the latest and greatest with all relevent bug fixes between releases, then download from https://github.com/dotproject/dotProject/archive/devel.zip

`master` tracks the current release.

**The Quick Way**: Point your browser to the `install` directory.

## Support

Support forums are at http://forums.dotproject.net/index.php

Bug reports and other issues can be lodged on GitHub at https://github.com/dotproject/dotProject/issues

IRC channel is irc://irc.freenode.net/dotproject on `#dotproject` on `irc.freenode.net`

## License

As of version 2.0, dotProject is released under GPL.
1.0.2 and previous versions were released under BSD license.
Parts of dotProject include libraries from other projects which are used and re-released under their original licence.

## Docker composer support

The latest devel branch now includes a simple docker-compose.yml file and support files.  These will allow you to run dotProject by running:

`docker-compose up`

This will set up an nginx container, a phpfpm container and a mariadb container and point the web server to the base directory of dotProject.  All you need to do after that is point your browser to http://localhost/

`docker-compose down` will retain any data you have created.  If you want to start again from a clean slate, use `docker-compose down -v` to remove the database volume.

