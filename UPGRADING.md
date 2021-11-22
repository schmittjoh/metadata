From 2.2.3 to 2.3.0
====================

- The `\Serializable` PHP interface is deprecated, the methods of this interface will be removed in 3.0.
  This change is done to allow the use of the new `__serialize` and `__unserialize` PHP's strategy.

  If you were extending the metadata classes, your custom serialization methods were looking probably as something as this:

    ```php
    class MyMetadata extends PropertyMetadata 
    {
        // ... more code
        
        public function serialize()
        {
            $data = parent::serialize();
    
            return \serialize([$data, $this->customMetadataProp]);
        }
    
        public function unserialize($str)
        {
            list($str, $this->customMetadataProp) = \unserialize($str);
    
            parent::unserialize($str);
        }
    }
    ```
    
    After this change, your code should look like this:
    
    ```php
    class MyMetadata extends PropertyMetadata 
    {
        // ... more code
        
        protected function serializeToArray(): array
        {
            $data = parent::serializeToArray();
    
            return [$data, $this->customMetadataProp];
        }
    
        protected function unserializeFromArray(array $data): void
        {
            list($data, $this->customMetadataProp) = $data;
    
            parent::unserializeFromArray($data);
        }
    }
    ```

From 1.7.0 to 2.0.0
====================

- Type-hinting everywhere where allowed by PHP 7.2 and strict types are used now
- `Metadata\Cache\CacheInterface` changed, methods have different names and signature; all the classes implementing 
that interface have been modified accordingly 
- `getValue` and `setValue` methods have been removed from `Metadata\PropertyMetadata`, getting/setting properties is not 
responsibility of this library anymore
- the `$reflection` property has been removed from `Metadata\PropertyMetadata`; 
metadata information do not require (and do not offer) reflection anymore
