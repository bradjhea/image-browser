# image-browser

This application is a light weight image browser. The aim of this application
is to be quick loading, clean looking and non-reliant on a traditional database.

This web application is built using PHP, jQuery, HTML, CSS w/ Bootstrap.

The back-end functionality does not need to be built with PHP and can be easily
replaced with another language or API.

# how does it work?

A JSON file (map.json) is used as a map of the images directory. This map file 
is used as a simple database. The map file is not created by default and must
be manually created using the command:

```

    http://your-website.com/backend/router?action=synchronise

```

Once your map file has been created you should be able to view your directory
structure and any corresponding images within it

# setting up

Below is an example of a typical folder structure:

```

- images/
    - gallery_1/
      - cover.jpg
      - sub_gallery_1_1/
          - image_1.jpg
          - image_2.jpg
          - image_3.jpg
          - image_4.jpg
          - image_5.jpg
    - gallery_2/
      - cover.jpg
      - sub_gallery_2_1/
          - image_1.jpg
          - image_2.jpg
          - image_3.jpg
          - image_4.jpg
          - image_5.jpg

```

Once an image directory has been created the application needs to know the
location of the images directory here is an example of a config file ...

```php

class config  {

    static function Create( $settingsObject) {
      return new self( $settingsObject);
    }

    public function __construct( $settingsObject ) {
        $settingsObject->folderTarget = "/images/";
    }

}

```

this file should be created within the folder /backend/config.php

Once your images directory has been populated with images and your config file
has been created you can begin to synchronise your images directory to the 
map.json file

```

    http://your-website.com/backend/router?action=synchronise

```