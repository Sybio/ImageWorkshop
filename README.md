# ================================
# ImageWorkshop class
# ================================

### Summary and features
Most flexible PHP class to work with images using the GD Library

http://phpimageworkshop.com/

### Latest updates

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

**Version 1.2.0 - 2012-07-31**
- Updating the resizeInPixel & resizeInPourcent methods: http://phpimageworkshop.com/doc/17/resizing.html
It allows you to resize your layer by conserving its proportion AND to get the wanted width and height both.
(So the layer will be resized to fit in the wanted dimensions)
- Creating the resize() method (refactoring resizeInPixel & Pourcent).

### Usage
You will find all the documentation here: http://phpimageworkshop.com/documentation.html & http://phpimageworkshop.com/tutorials.html

### @todo
- Layer reverse (horizontal or vertical flip)
- Adding a method to add easily borders to a layer (external, inside and middle border)

### Contributors
Main contributors:
- Clément Guillemain - Freelance [Sybio / @Sybio01]
          
And also:
- Cédric Spalvieri - Novaway [skwi69]
- Elton Minetto - Coderockr [eminetto]