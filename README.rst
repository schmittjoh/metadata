Metadata is a library for class/method/property metadata management in PHP
==========================================================================

Overview
--------

This library provides some commonly needed base classes for managing metadata
for classes, methods and properties. The metadata can come from many different
sources (annotations, YAML/XML/PHP configuration files).

The metadata classes are used to abstract away that source and provide a common
interface for all of them.

Usage
-----

The library provides three classes that you can extend to add your application
specific properties, and flags: ``ClassMetadata``, ``MethodMetadata``, and
``PropertyMetadata``

After you have added, your properties in sub-classes, you also need to add
``DriverInterface`` implementations which know how to populate these classes
from the different metadata sources.

Finally, you can use the ``MetadataFactory`` to retrieve the metadata::

    <?php
    
    use Metadata\MetadataFactory;
    use Metadata\Driver\DriverChain;
    
    $driver = new DriverChain(array(
        /** Annotation, YAML, XML, PHP, ... drivers */
    ));
    $factory = new MetadataFactory($driver);
    $metadata = $factory->getMetadataForClass('MyNamespace\MyObject');
    
