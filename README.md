# Ride: JSON API Library

JSON API library of the PHP Ride framework.

Check [http://jsonapi.org](http://jsonapi.org) for a full reference of the standard.

## What's In This Library?

### JsonApi

The _JsonApi_ class is the starting point for your implementation.
It's the container for the resource adapters and a factory for other instances of the library.
You should register your resource adapters before doing anything else with the API.

### JsonApiResourceAdapter

The only interface in this library is the _JsonApiResourceAdapter_ interface.
The implementations of this interface converts data entries from your data model into JSON API resources.
You need an instance of this interface for each data type you want to expose with your API.

### JsonApiResource

The _JsonApiResource_ class is the data container for a resource, eg a data entry from your data model.
The instances of this class are set to the responding document of an API request, either as a single instance, or in an array for a collection of resources.

### JsonApiDocument

The _JsonApiDocument_ is the container of the responding document of an API request.
You can set your resource(s) or error(s) as content to this document.
The content of your document can even be strictly meta.

The _JsonApiDocument_ instance holds your _JsonApi_ instance and a _JsonApiQuery_ instance.
It's passed on to the resource adapter when converting entries into resources.
Using the _JsonApi_ instance and the _JsonApiQuery_, the _JsonApiResourceAdapter_ can create the _JsonApiResource_ instance as the client requested.

### JsonApiQuery

Create a _JsonApiQuery_ instance from your incoming query parameters.
The _JsonApiQuery_ instance will give you easy access to the requested resources and pagination, sort and filter values.

## Code Sample

### Process The Incoming Request

A controller for your API could be something like this:

```php
<?php

include ride\library\http\jsonapi\JsonApi;

class BlogHandler {
    
    public function __construct(JsonApi $api) {
        $this->api = $api;
    }
    
    public function indexAction() {
        $query = $this->api->createQuery($_GET);
        $document = $this->api->createDocument($query);
        
        $document->setResourceCollection($this->getBlogPosts($query, $total));
        $document->setMeta('total', $total);
        
        http_status_code($document->getStatusCode());
        if ($document->hasContent()) {
            header('Content-Type: ' . JsonApi::CONTENT_TYPE);
    
            echo json_encode($document);
        }
    }
    
    private function getBlogPosts(JsonApiDocument $document, &$total) {
        // use the query to filter and manipulate the fetching of data
        $query = $document->getQuery();
        
        // static data as an example
        $blogPosts = array(
            1 => array(
                'id' => 1,
                'title' => 'Lorum Ipsum',
                'author' => array(
                    'id' => 1,
                    'name' => 'John Doe',
                ), 
                'tags' => array(
                    1 => array(
                        'id' => 1,
                        'name' => 'lorum',
                    ),
                    2 => array(
                        'id' => 2,
                        'name' => 'ipsum',
                    ),                    
                ),
            ),
            // ...
        );
        
        // count the total meta value
        $total = count($blogPosts);
        
        // apply pagination
        $blogPosts = array_slice($blogPosts, $query->getOffset(), $query->getLimit(10, 100));

        return $blogPosts;
    }

}
```

### Implement A Resource Adapter

```php
<?php

include ride\library\http\jsonapi\JsonApiDocument;
include ride\library\http\jsonapi\JsonApiResourceAdapter;

/**
 * Resource adapter for a blog post
 */
class BlogAdapter implements JsonApiResourceAdapter {
   
    /**
     * Type of this resource
     * @var string
     */ 
    const TYPE = 'blogs';
    
    /**
     * Gets a resource instance for the provided model data
     * @param mixed $data Data to adapt
     * @param JsonApiDocument $document Requested document
     * @param string $relationshipPath dot-separated list of relationship names
     * @return JsonApiResource
     */
    public function getResource($data, JsonApiDocument $document, $relationshipPath = null) {
        $api = $document->getApi();
        $query = $document->getQuery();

        // create the resource for the entry        
        $resource = $api->createResource(self::TYPE, $data['id']);
        $resource->setLink('self', 'http://your-resource-url');
        
        // check for an attribute and set when requested
        if ($query->isFieldRequested(self::TYPE, 'title')) {
            $resource->setAttribute('title', $data['title']);
        }

        // check for a relationship and set when requested        
        if ($query->isFieldRequested(self::TYPE, 'author') && $query->isIncluded($relationshipPath)) {
            // append the field to the relationship path
            $fieldRelationshipPath = ($relationshipPath ? $relationshipPath . '.' : '') . 'author';
            
            // retrieve the resource
            $peopleResourceAdapter = $api->getResourceAdapter('people');
            $author = $peopleResourceAdapter->getResource($data['author'], $document, $fieldRelationshipPath);
            
            // create the relationship 
            $relationship = $api->createRelationship();
            $relationship->setResource($author);
            $relationship->setLink('self', 'http://your-relationship-url');
            $relationship->setLink('related', 'http://your-related-url');
                        
            // add the relationship to your resource
            $resource->setRelationship('author', $relationship);
        }        
        
        // set a relationship collection value        
        if ($query->isFieldRequested(self::TYPE, 'tags') && $query->isIncluded($relationshipPath)) {
            $fieldRelationshipPath = ($relationshipPath ? $relationshipPath . '.' : '') . 'tags';
            $tagResourceAdapter = $api->getResourceAdapter('tags');
            
            $tags = $data['tags'];
            foreach ($tags as $tag) {
                $tags[$tagIndex] = $tagResourceAdapter->getResource($tag, $document);
            }
            
            $relationship = $api->createRelationship();
            $relationship->setResourceCollection($tags);
            $relationship->setLink('self', 'http://your-relationship-url');
            $relationship->setLink('related', 'http://your-related-url');
                                    
            $resource->setRelationship('tags', $relationship');
        }
        
        // return the resource
        return $resource;
    }
    
}
```

### Implementations

For more examples, you can check the following implementations of this library:
- [ride/wra](https://github.com/all-ride/ride-wra-queue)
- [ride/wra-app](https://github.com/all-ride/ride-wra-app)
- [ride/wra-i18n](https://github.com/all-ride/ride-wra-i18n)
- [ride/wra-orm](https://github.com/all-ride/ride-wra-orm)
- [ride/wra-queue](https://github.com/all-ride/ride-wra-queue)

## Installation

You can use [Composer](http://getcomposer.org) to install this library.

```
composer require ride/lib-http-jsonapi
```
