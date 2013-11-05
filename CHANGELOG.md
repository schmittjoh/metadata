CHANGELOG
=========

This changelog references all relevant changes:

To get the diff between the two last versions, go to 
https://github.com/schmittjoh/metadata/compare/1.0.0...1.1.0

* 1.5.0 (2013-11-06)
 * adds ability to inject new drivers into the DriverChain after it has been constructed
 * improves performance by removing some superfluous filesystem calls
 * made MetadataFactory implementation non-final

* 1.4.1 (2013-08-26)
 * fixes a possible permission issue when using filesystem ACLs

* 1.4.0 (2013-08-25)
 * fixes a race condition when writing cache files

* 1.3.0 (2013-01-22)
 * added ability to retrieve all managed classes from the metadata factory 

* 1.2.0 (2012-08-21)
 * added a Doctrine Cache Adapter
 * better support for traits, and classes in the global namespace

* 1.1.0 (2011-10-04)

 * added support for metadata on interfaces
 * added support for non annotation-based drivers
 * added support for merging metadata

This release is fully backwards compatible with the 1.0.0 release. Therefore,
the 1.0.x branch has been discontinued.

* 1.0.0 (2011-07-09)
