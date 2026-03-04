<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('StbLocales')) {
    class StbLocales
    {
        private array $locales = [];

        public function __construct()
        {
            $this->locales = [
                'core' => [
                    'yes' => __('Yes', 'wp-special-textboxes'),
                    'no' => __('No', 'wp-special-textboxes'),
                    'leftToRight' => __('Left-to-Right', 'wp-special-textboxes'),
                    'rightToLeft' => __('Right-to-Left', 'wp-special-textboxes'),
                    'saveTooltip' => __('Save', 'wp-special-textboxes'),
                ],
                'header' => [
                    'version' => __('Version', 'wp-special-textboxes'),
                    'dbVersion' => __('DB Version', 'wp-special-textboxes'),
                ],
                'footer' => [
                    'rights' => __('All rights reserved.', 'wp-special-textboxes'),
                ],
                'menu' => [
                    'styles' => __('Available Box Styles', 'wp-special-textboxes'),
                    'newStyle' => __('New Custom Color Style', 'wp-special-textboxes'),
                    'settings' => __('STB Settings', 'wp-special-textboxes'),
                    'themes' => __('Themes', 'wp-special-textboxes'),
                ],
                'appHeader' => [
                    'styles' => __('Styles', 'wp-special-textboxes'),
                    'settings' => __('Settings', 'wp-special-textboxes'),
                    'editor' => __('Colors Editor', 'wp-special-textboxes'),
                    'themes' => __('Themes', 'wp-special-textboxes'),
                ],
                'settings' => [
                    'boxSettings' => [
                        'boxSettingsCaption' => __('Box Settings', 'wp-special-textboxes'),
                        'marginsCaption' => __('Define Margins for Special Text Boxes', 'wp-special-textboxes'),
                        'marginsCaptions' => [
                            __('Top', 'wp-special-textboxes'),
                            __('Left', 'wp-special-textboxes'),
                            __('Right', 'wp-special-textboxes'),
                            __('Bottom', 'wp-special-textboxes'),
                        ],
                        'marginsTooltip' => __('This is a gap around of Special Text Box.', 'wp-special-textboxes'),
                        'borderSettings' => [
                            'borderSettingsCaption' => __('Define Border for Special Text Boxes', 'wp-special-textboxes'),
                            'borderStyleCaption' => __('Border Style', 'wp-special-textboxes'),
                            'borderStyleValues' => [
                                'solid' => __('Solid', 'wp-special-textboxes'),
                                'dashed' => __('Dashed', 'wp-special-textboxes'),
                                'dotted' => __('Dotted', 'wp-special-textboxes'),
                                'none' => __('None', 'wp-special-textboxes'),
                            ],
                            'borderWidthCaption' => __('Border Width', 'wp-special-textboxes'),
                            'roundedCornersCaption' => __('Allow rounded corners', 'wp-special-textboxes'),
                            'borderRadiusCaption' => __('Border Radius', 'wp-special-textboxes'),
                            'borderSettingsTooltip' => __('Use these parameters for customising Special Text Box borders.', 'wp-special-textboxes'),
                        ],
                        'collapsingCaption' => __('Allow collapsing/expanding captioned Special Text Boxes', 'wp-special-textboxes'),
                        'collapsedCaption' => __('Allow "collapsed on load" captioned Special Text Boxes', 'wp-special-textboxes'),
                    ],
                    'imagesSettings' => [
                        'imagesSettingsCaption' => __('Images Settings', 'wp-special-textboxes'),
                        'imgMinusTitle' => __('Define Hide Tool Image', 'wp-special-textboxes'),
                        'imgMinusCaption' => __('Hide Tool Image', 'wp-special-textboxes'),
                        'imgMinusCheckCaption' => __('Use Default Hide Tool Image', 'wp-special-textboxes'),
                        'imgMinusTooltip' => __('This image is displayed in the text block header and shows the status of the non collapsed text block.', 'wp-special-textboxes'),
                        'imgPlusTitle' => __('Define Show Tool Image', 'wp-special-textboxes'),
                        'imgPlusCaption' => __('Show Tool Image', 'wp-special-textboxes'),
                        'imgPlusCheckCaption' => __('Use Default Show Tool Image', 'wp-special-textboxes'),
                        'imgPlusTooltip' => __('This image is displayed in the text block header and shows the status of the collapsed text block.', 'wp-special-textboxes'),
                        'durationCaption' => __('Animation Duration', 'wp-special-textboxes'),
                        'durationTooltip' => __('This is time of collapsing/expanding of the text block in milliseconds.', 'wp-special-textboxes'),
                        'bigImgCaption' => __('Allow Big Image for Simple (non-captioned) Special Text Boxes', 'wp-special-textboxes'),
                        'bigImgTooltip' => __('Selecting "Yes" will allow big icons for simple (non-captioned) Special Text Boxes.', 'wp-special-textboxes'),
                        'showImgCaption' => __('Allow icon images for Special Text Boxes', 'wp-special-textboxes'),
                        'showImgTooltip' => __('Selecting "Yes" will allow displaying icon images in Special Text Boxes.', 'wp-special-textboxes'),
                        'sideCaption' => __('Allow caption background colors for side image background (boxes without caption only)', 'wp-special-textboxes'),
                    ],
                    'textSettings' => [
                        'caption' => __('Text Settings', 'wp-special-textboxes'),
                        'textFontCaption' => __('Body Text Settings', 'wp-special-textboxes'),
                        'textFontSizeLabel' => __('Define text font size', 'wp-special-textboxes'),
                        'textFontFamilyLabel' => __('Define text font family', 'wp-special-textboxes'),
                        'captionFontCaption' => __('Caption Text Settings', 'wp-special-textboxes'),
                        'captionFontSizeLabel' => __('Define caption font size', 'wp-special-textboxes'),
                        'captionFontFamilyLabel' => __('Define caption font family', 'wp-special-textboxes'),
                        'fontSizeHelperText' => __('This is font size in pixels. Set this parameter to value 0 for theme default font size.', 'wp-special-textboxes'),
                        'fontFamilyHelperText' => __('This is font family for box text.', 'wp-special-textboxes'),
                        'languageDirectionCaption' => __('Define language direction', 'wp-special-textboxes'),
                        'languageDirectionTooltip' => __('Selecting "Left-to-Right" will set Left-to-Right language direction for Special Text Boxes and visa versa.', 'wp-special-textboxes'),
                    ],
                    'shadowsSettings' => [
                        'caption' => __('Shadows Settings', 'wp-special-textboxes'),
                        'boxShadowCaption' => __('Define Box Shadow for Special Text Boxes', 'wp-special-textboxes'),
                        'boxShadowLabel' => __('Enable Box Shadow', 'wp-special-textboxes'),
                        'textShadowCaption' => __('Define Text Shadow for Special Text Boxes', 'wp-special-textboxes'),
                        'textShadowLabel' => __('Enable Text Shadow', 'wp-special-textboxes'),
                        'shadowSettings' => [
                            'insideShadow' => __('Drop shadow inside the box', 'wp-special-textboxes'),
                            'offsetX' => __('Offset X', 'wp-special-textboxes'),
                            'offsetY' => __('Offset Y', 'wp-special-textboxes'),
                            'blur' => __('Shadow Blur', 'wp-special-textboxes'),
                            'color' => __('Shadow Color', 'wp-special-textboxes'),
                            'shadow' => __('Shadow', 'wp-special-textboxes'),
                            'preview' => __('preview', 'wp-special-textboxes'),
                        ],
                    ],
                    'systemSettings' => [
                        'caption' => __('System Settings', 'wp-special-textboxes'),
                        'loadingCaption' => __('Define mode of CSS loading', 'wp-special-textboxes'),
                        'staticCaption' => __('Static', 'wp-special-textboxes'),
                        'clientDynamicCaption' => __('Client Dynamic', 'wp-special-textboxes'),
                        'serverDynamicCaption' => __('Server Dynamic', 'wp-special-textboxes'),
                        'staticTooltip' => __('will be loaded static styles sheet file. More faster but needs full read/write access to file.', 'wp-special-textboxes'),
                        'clientDynamicTooltip' => __('styles sheet will be loaded dynamically on the client side.', 'wp-special-textboxes'),
                        'serverDynamicTooltip' => __('styles sheet will be loaded dynamically on the server side.', 'wp-special-textboxes'),
                    ],
                    'deactivationSettings' => [
                        'caption' => __('Deactivation Settings', 'wp-special-textboxes'),
                        'actionsString' => __('Are you allow to perform these actions during deactivating plugin?', 'wp-special-textboxes'),
                        'optionsCaption' => __('Delete plugin options during plugin deactivation', 'wp-special-textboxes'),
                        'dbCaption' => __('Delete database tables of plugin during plugin deactivation', 'wp-special-textboxes'),
                    ],
                ],
                'styles' => [
                    'filterAll' => __('All', 'wp-special-textboxes'),
                    'filterActive' => __('Active', 'wp-special-textboxes'),
                    'filterTrash' => __('Trash', 'wp-special-textboxes'),
                    'itemAlias' => __('Alias', 'wp-special-textboxes'),
                    'itemType' => __('Type', 'wp-special-textboxes'),
                    'noData' => __('No data to show...', 'wp-special-textboxes'),
                ],
                'editor' => [
                    'namesSection' => [
                        'caption' => __('Names', 'wp-special-textboxes'),
                        'captionCaption' => __('Default Caption', 'wp-special-textboxes'),
                        'captionTooltip' => __('Enter Default Caption for this Style.', 'wp-special-textboxes'),
                        'nameCaption' => __('Name', 'wp-special-textboxes'),
                        'nameTooltip' => __('Unique name of this style', 'wp-special-textboxes'),
                        'nameErrorTooltip' => __('Only latin symbols, digits, "-" and "_" are accepted', 'wp-special-textboxes'),
                        'typeCaption' => __('Type', 'wp-special-textboxes'),
                        'trashInTrash' => __('In trash', 'wp-special-textboxes'),
                        'trashActive' => __('Active', 'wp-special-textboxes'),
                    ],
                    'borderImageSection' => [
                        'caption' => __('Border and Icon', 'wp-special-textboxes'),
                        'colorCaption' => __('Border Color', 'wp-special-textboxes'),
                        'colorTooltip' => __('This is border color.', 'wp-special-textboxes'),
                        'imageTitle' => __('Icon', 'wp-special-textboxes'),
                        'imageCaption' => __('Icon URL', 'wp-special-textboxes'),
                        'imageCheckCaption' => __('Use Default Icon', 'wp-special-textboxes'),
                        'imageTooltip' => __('I you don\'t want to use default icon, fill this field and uncheck "Use Default Icon" selector.', 'wp-special-textboxes'),
                    ],
                    'colorsSection' => [
                        'bodyTitle' => __('Body', 'wp-special-textboxes'),
                        'captionTitle' => __('Caption', 'wp-special-textboxes'),
                        'basicColorsSection' => [
                            'gradientTitle' => __('Background Gradient Colors', 'wp-special-textboxes'),
                            'gradientStartCaption' => __('Start Color', 'wp-special-textboxes'),
                            'gradientStopCaption' => __('Stop Color', 'wp-special-textboxes'),
                            'gradientTooltip' => __('There are colors of box background gradient. Direction of gradient drawing is from top to bottom.', 'wp-special-textboxes'),
                            'fontColorCaption' => __('Text Color', 'wp-special-textboxes'),
                        ],
                    ],
                ],
                'messages' => [
                    'settings' => [
                        'savingSuccess' => __('Plugin settings are successfully saved', 'wp-special-textboxes'),
                        'savingError' => __('An error occurred during saving process', 'wp-special-textboxes'),
                    ],
                    'editor' => [
                        'savingSuccess' => __('Style is successfully saved', 'wp-special-textboxes'),
                        'savingError' => __('An error occurred during saving process', 'wp-special-textboxes'),
                    ],
                    'themes' => [
                        'savingSuccess' => __('Theme is successfully activated.', 'wp-special-textboxes'),
                        'savingError' => __('An error occurred during activating theme...', 'wp-special-textboxes'),
                    ],
                ],
            ];
        }

        public function getLocales(): array
        {
            return $this->locales;
        }
    }
}
