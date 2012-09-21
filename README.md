# ================================
# ImageWorkshop class
# ================================

### Summary and features
Really flexible and easy-to-use PHP class to work with images using the GD Library

http://phpimageworkshop.com/

### Latest updates

**Version 1.2.5 - 2012-09-21**
- Adding ImageWorkshop on http://travis-ci.org/ for controlled continuous integration

**Version 1.2.5 - 2012-09-18**
- Fixing the only one known bug that we are tracking for a long time: you can know
apply a rotation (->rotate()) and then an opacity (->opacity()) on a layer without crash.
- Updating composer.json to autoload the class in a project using composer
- Testing if the PHP environment has GD library enabled

**Version 1.2.4 - 2012-09-03**
- Adding the changePosition() method, to redefine the position of a sublayer in a layer stack
- Adding the applyTranslation() method, that result to new positions after a given translation
- Updating the getLayerPositions() method that allow you to get the positions of a sublayer
- Refactoring methods that change the position of a sublayer for the new method
- Adding some informations in the composer file

**Version 1.2.3 - 2012-08-30**
- Fixing a position bug when cropping with some positioning choices on crop() method
- Changing the comportment of updateLayerPositionsAfterCropping(), an internal method
- Removing useless cropBackground() method
- Refactoring crop() method (comportment doesn't change)
- Updating the lib path in the test file

**Version 1.2.2 - 2012-08-16**
- Fixing a bug when applying a filter because of given parameter number

**Version 1.2.1 - 2012-08-13**
- Better image quality after resize. (replace imagecopyresized function for imagecopyresampled)

### Usage

- Learn how to use the class in 5 minutes: http://phpimageworkshop.com/quickstart.html
- The complete documentation: http://phpimageworkshop.com/documentation.html
- Usefull tutorials: http://phpimageworkshop.com/tutorials.html

**What's new in the doc' ?**

- Rewriting the tutorial "Creating thumbnails": http://phpimageworkshop.com/tutorial/2/creating-thumbnails.html
- Tutorial "Beautify your images with filters": http://phpimageworkshop.com/tutorial/4/beautify-images-filters.html
- Quickstart page to learn the class faster: http://phpimageworkshop.com/quickstart.html

### @todo
- Layer reverse (horizontal or vertical flip)
- Adding a method to add easily borders to a layer (external, inside and middle border)

### Contributors
Main contributors:
- Clément Guillemain - Freelance [Sybio / @Sybio01]
          
And also:
- Cédric Spalvieri - Novaway [skwi69]
- Elton Minetto - Coderockr [eminetto]
- Phil Sturgeon - HappyNinjas Ltd. [philsturgeon]
- [ziadoz]
