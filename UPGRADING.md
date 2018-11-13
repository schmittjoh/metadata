From 1.7.0 to 2.0.0
====================

- Type-hinting everywhere where allowed by PHP 7.2 and strict types are used now
- `Metadata\Cache\CacheInterface` changed, methods have different names and signature; all the classes implementing 
that interface have been modified accordingly 
- `getValue` and `setValue` methods have been removed from `Metadata\PropertyMetadata`, getting/setting properties is not 
responsibility of this library anymore
- the `$reflection` property has been removed from `Metadata\PropertyMetadata`; 
metadata information do not require (and do not offer) reflection anymore
