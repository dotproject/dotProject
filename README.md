# dotProject+ ("plus")
This is an unofficial dotProject+ repository, built predominantly for the purpose
of identifying how the current release differs from [dotProject](https://github.com/dotproject/dotProject) core.

dotProject+ aligns dP core with PMBOK and CMMI-DEV guidelines/methodologies for Project Management.

## dotProject+ Resources
* [Overview](http://www.gqs.ufsc.br/evolution-of-dotproject/)
* [Manual ~2012](http://www.gqs.ufsc.br/wp-content/uploads/2012/03/Manual_dotProject+_v02a_english.pdf)
* [Publications](http://www.gqs.ufsc.br/dotproject-publications/)
* [Forum Announcement](http://forums.dotproject.net/showthread.php?p=46899)

## Goals for this fork
dotProject+ has made some significant core changes, and module additions to dotProject core.
De Bortoli Wines in conjunction with 2pi Software, the dotProject core developers (and hopefully others) are hoping to achieve the following:
* break down core changes into smaller pieces/commits related to specific fixes/features
* break out the modules into individual modules where possible
* submit these additions as a series of small pull requests to the core dotProject codebase
* a parallel investigation into adding support for PHP7 is also taking place

In all cases, the intention is to work with the dotProject developers to make the fixes/features palatable for acceptance into the core project.

## Repository Outline
Branches: 
* master - based on dotProject core, stacked with any *completed* features/fixes split out of the dotProject_plus codebase
* dotproject_plus - based on the current dotProject+ release, cleaned/sanitised for easier difference comparisons
* various *incomplete* feature/fix branches split from the original monolithic diff between the two projects
* *incomplete* php7 migration progress

# About dotProject

## dotProject
dotProject is an open source project management system written in PHP.

It originally started in 2001 by dotMarketing on SourceForge and has
been under the watchful eye of the current dotProject team since around
since around December 2002.

## Installing/Upgrading

**The Quick Way**: Point your browser to the `install` directory.

## (dotProject) License

As of version 2.0, dotProject is released under GPL.
1.0.2 and previous versions were released under BSD license.
Parts of dotProject include libraries from other projects which are used and re-released under their original licence.
