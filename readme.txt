=== Special Text Boxes ===
Contributors: minimus
Donate link:  https://load.payoneer.com/LoadToPage.aspx?email=minimus@simplelib.com
Tags: content, performance, text, code, php, widget
Requires at least: 3.5
Tested up to: 3.8.1
Stable tag: 5.0.85
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Highlights any portion of text as text in the colored boxes.

== Description ==

Adds little style sheet file and short code to blog for highlighting some portion of text in post as colored boxes. That may be warning, alert, info and download portion of post's text.

**WARNING!!!** **Special Text Widget** works only under **Wordpress 2.8+** !

Available languages:

* English
* Russian
* Italian by [Gianni Diurno](http://gidibao.net/)
* Belarus by [Fat Cower](http://www.fatcow.com)
* Uzbek by [Alisher Safarov](http://www.comfi.com)
* Polish by [Daniel Fruzynski](http://www.poradnik-webmastera.com)
* Arabic by [مدونة رسين](http://www.r-sn.com/wp/)
* Dutch by [Rene](http://wpwebshop.com/blog/)
* Ukrainian by [official ukrainian localization team](http://wordpress.co.ua/)
* German by [Renate](http://www.bhvnederland.nl/)
* Czech by [Stanislav Čihák](http://www.abacomp.cz/)
* Turkish by Serhat ESEN
* Slovak by [Branco](http://webhostinggeeks.com/blog/)
* Traditional Chinese by 断青丝
* Spanish by [Andrew Kurtis](http://www.webhostinghub.com/)

If you have created your own language pack, or have an update of an existing one, you can send **.po** and **.mo files** to me (minimus AT simplelib.com) so that I can bundle it into **Special Text Boxes**.
  
Real examples of outputs you can see on the [plugin page](http://www.simplelib.com/?p=11)

== Installation ==

1. Upload plugin dir to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Use short codes in the text of post to highlight any portion of it.

== Frequently Asked Questions ==

= How to insert special text box to post's text? =

Use short codes: 

`[stextbox id="ValidID"]Highlighted text here[/stextbox]`

or (for captioned textbox)

`[stextbox id="ValidID" caption="CaptionText"]Highlighted text here[/stextbox]`

were
 
**ValidID** may be: *alert*, *info*, *download*, *grey*, *black*, *warning*, *custom*

**CaptionText** may be: *any text you needed*.

= How to insert special text box to theme file (not to post)? =

Use function **stbHighlightText**: 

`<?php if(function_exists('stbHighlightText')) stbHighlightText('Test of function stbHighlightText.', 'warning'); ?>` 

defined as 

`function stbHighlightText( $content = null, $id = 'warning', $caption = '', $atts = null )`

= Can I use Special Text widget with Wordpress 2.7? =

No! *Special Text widget* wrote with using *Wordpress Widget Factory technology* and this one can work only under Wordpress 2.8 and higher!

= How to insert special text box into special text box? =

Use *stb* shortcode inside *stextbox* shortcode

`[stextbox]Some text [stb]Some text in the indoor box[/stb][/stextbox]`

More about Special Text Boxes usage and customising read on the [plugin page](http://www.simplelib.com/?p=11)

== Screenshots ==

1. Examples of special textboxes
1. Special Text Boxes Parameters Page
1. Special Text Boxes Box Editor
1. Insertion dialog. Basic Settings
1. Insertion dialog. Extended Settings
1. Special Text widget. Admin Page


== Changelog ==

= 5.0.85 =
= 4.5.81 =
* Major bug is fixed
= 4.5.80 =
* CSS mode is optimized
* Output codes are optimized
* Media Loader is added
* Language pack is updated. Spanish by Andrew Kurtis is added.
* Some bugs are fixed
= 4.4.75 =
* Text Line Height settings are added.
* Some bugs are resolved.
= 4.3.73 =
* Language pack is updated. Slovak by Branco is added
= 4.3.72 =
* Static Style Sheet mode (CSS mode) is added
* User Level is changed to Capability
= 4.2.70 =
* Bug of STB output function is fixed.
= 4.1.69 =
* Some bugs are fixed.
* Control of block's shadow from short codes is added.
* Improvements in the interface of plugin are made.
* Compatibility with "Wordpress Post Tabs" plugin is added.
= 4.0.65 =
* Styles repository is added
* Javascript drawing mode is added
* New Styles Editor is added
* New rules of text blocks drawing are added
* Some bugs are resolved
= 3.10.60 =
* Italian language pack is updated.
= 3.10.59 =
* Minor bug are fixed
* Language pack is updated. Turkish by Serhat ESEN is added
= 3.9.57 =
* The amount of the parameters adjusted "on the fly" is increased
* The output html code meets the requirements of the markup validity of Web documents
= 3.8.55 =
* Now the plugin codes are using **Wordpress 3.0+** standards (PHP5 only)
* Wordpress Settings API are used
* Now the plugin uses "resources saving technology" for saving server resources in blog runtime mode.
= 3.7.52 =
* Language pack is updated. Czech by [Stanislav Čihák](http://www.abacomp.cz/) are added.
= 3.7.51 =
* Tool Button bug are fixed.
* STB in STB shortcodes are added.
= 3.6.49 =
* Collapsed mode bug are fixed.
* Special Text Widget bug are fixed.
* Language pack is updated. Ukrainian by [official ukrainian localization team](http://wordpress.co.ua/) is added.
* Language pack is updated. German by [Renate](http://www.bhvnederland.nl/) is added.
= 3.5.45 =
* Language pack is updated. Dutch by [Rene](http://wpwebshop.com/blog/) are added.
= 3.5.44 =
* Floating mode bug are fixed
* **stbHighlightText** function bug (collapsed option) are fixed
* CSS3 styles are fixed
= 3.4.41 =
* Insertion Dialog bug are fixed
= 3.4.40 =
* Font size parameters are added
* Bug of caption size is eliminated
* Language pack is updated. Arabic language by [مدونة رسين](http://www.r-sn.com/wp/) is added. 
* Support of text direction is added.
* Codes are optimised
= 3.3.35 =
* Collapsing/Expanding mode of captioned Special Text Boxes was extended
* PHP codes was optimised
* JS codes was optimised
= 3.2.32 =
* Collapsing/Expanding of captioned Special Text Boxes was added
* Codes was optimised
= 3.1.29 =
* Some admin page improvements was added
* Codes was optimised
= 3.0.27 =
* Special Text widget was added
* Special Text Box Float Mode was added
= 2.0.25 =
* Polish language pack by [Daniel Fruzynski](http://www.poradnik-webmastera.com) added
* Italian language pack updated
= 2.0.23 =
* Uzbek language pack by [Alisher Safarov](http://www.comfi.com) added
* Wordpress 2.8.4 compatibility tested
= 2.0.22 =
* Direct output codes optimised
* Italian language pack updated
= 2.0.20 =
* Plugin style sheet optimised
* Big icons for simple (non-captioned) boxes added
* Short Codes Insert Dialog added
* Output function added
* Plugin codes optimised
= 1.2.13 =
* Belarus language by [Fat Cower](http://www.fatcow.com) added
* Wordpress 2.8.1 compatibility tested
= 1.2.12 =
* Italian language by [Gianni Diurno](http://gidibao.net/) added
= 1.2.11 =
* custom box added
* custom editor added
* customising "on the fly" added
* Wordpress 2.8 compatibility checked
= 1.1.7 =
* black box margins bug fixed
= 1.1.6 =
* codes and variables cleanup
	* admin page codes optimised
	* activation codes optimised for future upgrades
* margin settings added
= 1.0.1 =
* Initial upload

== Upgrade Notice ==

= 5.0.85 =
= 4.5.81 =
Major bug is fixed.
= 4.5.80 =
Some new features.
= 4.4.75 =
Text Line Height settings are added.
Some bugs are resolved.
= 4.3.73 =
Language pack is updated. Slovak by Branco is added.
= 4.3.72 =
Static Style Sheet mode (CSS mode) is added.
User Level is changed to Capability.
= 4.2.70 =
Bug of STB output function is fixed. 
= 4.1.69 =
Some bugs are fixed. 
Control of block's shadow from short codes is added. 
Improvements in the interface of plugin are made. 
Compatibility with "Wordpress Post Tabs" plugin is added.  
= 4.0.65 =
Styles repository is added. 
Javascript drawing mode is added. 
New Styles Editor is added. 
New rules of text blocks drawing are added. 
Some bugs are resolved. 
= 3.10.60 =
Italian language pack is updated.
= 3.10.59 =
Minor bug are fixed. 
Language pack is updated. Turkish by Serhat ESEN is added. 
= 3.9.57 =
The amount of the parameters adjusted "on the fly" is increased 
The output html code meets the requirements of the markup validity of Web documents 
= 3.8.55 =
Now the plugin codes are using Wordpress 3.0+ standards (PHP5 only). 
Wordpress Settings API are used. 
Now the plugin uses "resources saving technology" for saving server resources in blog runtime mode. 
= 3.7.52 =
Language pack is updated. Czech by [Stanislav Čihák](http://www.abacomp.cz/) are added.
= 3.7.51 =
Tool Button bug are fixed.
STB in STB shortcodes are added.
= 3.6.49 =
Collapsed mode bug are fixed.
Special Text Widget bug are fixed.
Language pack is updated. Ukrainian by [official ukrainian localization team](http://wordpress.co.ua/) is added.
Language pack is updated. German by [Renate](http://www.bhvnederland.nl/) is added.
= 3.5.45 =
Dutch language are added
= 3.5.44 =
Floating mode bug are fixed
stbHighlightText function bug (collapsed option) are fixed
CSS3 styles are fixed
= 3.4.41 =
Insertion Dialog bug are fixed
= 3.4.40 =
Font size parameters are added
Bug of caption size is eliminated
Language pack is updated. Arabic language by [مدونة رسين](http://www.r-sn.com/wp/) is added.
Support of text direction is added.
Codes are optimised
= 3.3.35 =
Collapsing/Expanding mode of captioned Special Text Boxes was extended
PHP codes was optimised
JS codes was optimised
= 3.2.32 =
Collapsing/Expanding of captioned Special Text Boxes was added
Codes was optimised
= 3.1.29 =
Some admin page improvements was added
Codes was optimised
= 3.0.27 =
Special Text widget was added
Special Text Box Float Mode was added
= 2.0.25 =
Polish language pack by [Daniel Fruzynski](http://www.poradnik-webmastera.com) added
Italian language pack updated
= 2.0.23 =
Uzbek language pack by [Alisher Safarov](http://www.comfi.com) added
Wordpress 2.8.4 compatibility tested
= 2.0.22 =
Direct output codes optimised
Italian language pack updated
= 2.0.20 =
Plugin style sheet optimised
Big icons for simple (non-captioned) boxes added
Short Codes Insert Dialog added
Output function added
Plugin codes optimised
= 1.2.13 =
Belarus language by [Fat Cower](http://www.fatcow.com) added
Wordpress 2.8.1 compatibility tested
= 1.2.12 =
Italian language by [Gianni Diurno](http://gidibao.net/) added
= 1.2.11 =
custom box added
custom editor added
customising "on the fly" added
Wordpress 2.8 compatibility checked
= 1.1.7 =
black box margins bug fixed
= 1.1.6 =
codes and variables cleanup
admin page codes optimised
activation codes optimised for future upgrades
margin settings added
= 1.0.1 =
Initial upload
	
== Other Notes ==

This plugin is using jQuery ColorPicker plugin by [Stefan Petre](http://www.eyecon.ro)