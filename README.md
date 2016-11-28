berthe  [![Travis](https://img.shields.io/travis/Evaneos/berthe.svg?branch=master)](https://travis-ci.org/Evaneos/berthe) 
======


# Utils

### Buffered Iterator

Useful when your logic can directly process a batch of result instead of one by one
 
```php
$bufferIterator = new BufferedIterator(new FetcherIterator(new Service(), new FetcherBuilder(), 100);

foreach($bufferIterator as $results){
    dump($results); //contains 100 items
    
    $resource = new Resource($results, $composerName);
    $composed = $composerManager->compose($resource);
    $composedResource = $composed->getComposite();
    $composedResource = new Collection($composedResource, $transformer);
    $transformedRessource = $fractal->createData($composedResource)->toArray();
    
    //got your transformed batch instead of processing one by one or any overhead
}
```
