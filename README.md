# ================================
# ImageWorkshop class
# ================================

### Summary and features
Most flexible PHP class to work with images using the GD Library

http://phpimageworkshop.com/

### Latest updates

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

- Learn how to use the class in 5 minutes: http://phpimageworkshop.com/quickstart.html
- The complete documentation: http://phpimageworkshop.com/documentation.html
- Usefull tutorials: http://phpimageworkshop.com/tutorials.html

**What's new in the doc' ?**

- Rewriting the tutorial "Creating thumbnails": http://phpimageworkshop.com/tutorial/2/creating-thumbnails.html
- Tutorial "Beautify your images with filters": http://phpimageworkshop.com/tutorial/4/beautify-images-filters.html
- Quickstart page to learn the class faster: http://phpimageworkshop.com/quickstart.html

### @todo
- Adding a method to add easily borders to a layer (external, inside and middle border)

### Contributors
Main contributors:
- Clément Guillemain - Freelance [Sybio / @Sybio01]
          
And also:
- Cédric Spalvieri - Novaway [skwi69]
- Elton Minetto - Coderockr [eminetto]