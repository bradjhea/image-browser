# image-browser

This application is used to traverse through folders and load their respective
images within.

A directory should be setup within the root folder before any other work is 
carried out

Once an image directory has been created the application needs to know the
location of the images directory here is an example of a config file ...

<code>

class config  {

    static function Create( $settingsObject) {
      return new self( $settingsObject);
    }

    public function __construct( $settingsObject ) {
        $settingsObject->folderTarget = "/images/";
    }

}

</code>

this file should be created within the folder backend/

Below is an example of the folder structure:

- images/
    - gallery_1/
      - cover.jpg
      - sub_gallery_1_1/
          - image_1.jpg
          - image_2.jpg
          - image_3.jpg
          - image_4.jpg
          - image_5.jpg
