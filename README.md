# Ride: JSON API Library

JSON API library of the PHP Ride framework.
Check http://jsonapi.org for a full reference of the standard.

## How To Use This Library

### Process The Incoming Request

```php
<?php

include ride\library\http\jsonapi\JsonApi;

class BlogHandler {
    
    public function __construct(JsonApi $api) {
        $this->api = $api;
    }
    
    public function indexAction() {
        $query = $api->createQuery($_GET);
        $document = $api->createDocument($query);
        
        $document->setResourceCollection($this->getBlogs($query));
        
        http_status_code($document->getStatusCode());
        if ($document->hasContent()) {
            header('Content-Type: ' . JsonApi::CONTENT_TYPE);
    
            echo json_encode($document);
        }
    }
    
    private function getBlogs(JsonApiQuery $query) {
        // static resource for example, use the query to filter and manipulate the fetching of data
        return array(
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
    }

}

### Implement An Adapter

The _JsonApiResourceAdapter_ interface provides a way to convert the entries of your model to a resource usable by the JSON API.
 

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
        
        // check for an attribute and set when requested
        if ($query->isFieldRequested(self::TYPE, 'title')) {
            $resource->setAttribute('title', $data['title']);
        }

        // check for a relationship and set when requested        
        if ($query->isFieldRequested(self::TYPE, 'author') && $query->isIncluded($relationshipPath) && $api->increaseLevel()) {
            // as last condition there is a increaseLevel, you can use this to determine the depth of recursiveness
            $peopleResourceAdapter = $api->getResourceAdapter('people');
            
            $author = $peopleResourceAdapter->getResource($data['author'], $document);
            
            $relationship = $api->createRelationship();
            $relationship->setResourceCollection($author);
            
            // don't forget to decrease the level
            $api->decreaseLevel();
        }        
        
        // set a relationship collection value        
        if ($query->isFieldRequested(self::TYPE, 'tags') && $query->isIncluded($relationshipPath) && $api->increaseLevel()) {
            $tagResourceAdapter = $api->getResourceAdapter('tags');
            
            $tags = $data['tags'];
            foreach ($tags as $tag) {
                $tags[$tagIndex] = $tagResourceAdapter->getResource($tag, $document);
            }
            
            $relationship = $api->createRelationship();
            $relationship->setResourceCollection($tags);
            
            $api->decreaseLevel();
        }
        
        // return the resource
        return $resource;
    }
    
}
