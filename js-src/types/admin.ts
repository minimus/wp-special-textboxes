export interface TAction {
  type: string;
  payload?: unknown | Record<string, unknown> | [] | number | string | boolean;
  error?: unknown;
  act?: unknown;
}

export type TISODate = string;

export type TPromiseAction = Promise<TAction>;

export type TThunkAction = (TDispatch) => unknown;

export type TDispatch = (action: TAction | TPromiseAction | TThunkAction | Array<TAction>) => void;

export interface TBodyColors {
  color: string;
  background: string[];
}

export interface TImage {
  enabled: boolean;
  image: string;
  defaultImage: string;
}

export interface TColors {
  body: TBodyColors;
  border: {
    color: string;
  };
  caption: TBodyColors;
  image: TImage;
}

export interface TSysInfo {
  dbVersion: string;
  memoryLimit: string;
  phpVersion: string;
  sqlVersion: string;
  version: string;
  wpMemoryLimit: string;
  wpMemoryLimitMax: string;
}

export interface TMarginsSettings {
  top: number;
  left: number;
  right: number;
  bottom: number;
}

export interface TShadowSettings {
  enabled: boolean;
  inset?: boolean;
  offsetX: number;
  offsetY: number;
  blur: number;
  alpha?: number;
  color: string;
}

export interface TFontSettings {
  fontSize: number;
  fontFamily: string;
}

export interface TCaptionSettings {
  font: TFontSettings;
}

export interface TTextSettings {
  shadow: TShadowSettings;
  font: TFontSettings;
  height: string;
  customHeight: number;
}

export interface TImageSettings {
  image: string;
  defaultImage: string;
  enabled: boolean;
}

export interface TSettings {
  themeName: string;
  roundedCorners: boolean;
  radius: number;
  // textShadow: boolean
  // boxShadow: boolean
  borderStyle: string;
  borderWidth?: number;
  margins: TMarginsSettings;
  bigImg: boolean;
  showImg: boolean;
  collapsing: boolean;
  collapsed: boolean;
  // fontSize: number
  // captionFontSize: number
  langDirect: 'ltr' | 'rtl';
  mode: 'css' | 'js';
  side: number;
  imgMinus: TImageSettings;
  imgPlus: TImageSettings;
  duration: number;
  caption: TCaptionSettings;
  shadow: TShadowSettings;
  text: TTextSettings;
  deleteOptions: number;
  deleteDB: number;
  cssLoading: 'static' | 'styled' | 'dynamic';
}

export interface TRestData {
  root: string;
  nonce: string;
}

export interface TOptionsTexts {
  ok: string;
  cancel: string;
  switchModeToNum: string;
  switchModeToCol: string;
}

export interface TOptionsMedia {
  title: string;
  button: string;
}

export interface TOptionsPluginData {
  root: string;
}

export interface TOptions {
  restData: TRestData | null;
  texts: TOptionsTexts | null;
  media: TOptionsMedia;
  pluginData: TOptionsPluginData;
}

export interface TWpMediaAttachmentSizes {
  url: string;
  height: number;
  width: number;
  orientation: string;
}

export interface TWpMediaAttachment {
  id: number;
  title: string;
  filename: string;
  url: string;
  link: string;
  alt: string;
  author: string;
  description: string;
  caption: string;
  name: string;
  status: string;
  uploadedTo: number;
  date: Date;
  modified: Date;
  menuOrder: number;
  mime: string;
  type: string;
  subtype: string;
  icon: string;
  dateFormatted: string;
  nonces: {
    update: string;
    delete: string;
    edit: string;
  };
  editLink: string;
  meta: boolean;
  authorName: string;
  filesizeInBytes: number;
  filesizeHumanReadable: string;
  context: string;
  height: number;
  width: number;
  orientation: string;
  sizes: {
    full: TWpMediaAttachmentSizes;
  };
  compat: {
    item: string;
    meta: string;
  };
}

export interface IWpMedia {
  on: (act: string, cb: () => void) => IWpMedia;
  open: () => IWpMedia;
  state: () => IWpMedia;
  get: (string) => IWpMedia;
  first: () => IWpMedia;
  toJSON: () => TWpMediaAttachment;
}

export type TBackground = string[];

export interface TElementColors {
  color: string;
  background?: TBackground;
}

export interface TStyleColors {
  body: TElementColors;
  border: TElementColors;
  caption: TElementColors;
  image: TImageSettings;
}

export interface TStyle {
  slug: string;
  type: string;
  caption: string;
  colors: TStyleColors;
  trash: 0 | 1;
}

export interface TThemeInfo {
  name: string;
  slug: string;
  description: string;
  image: string;
}

export type TEventValue = Record<string, unknown>;

export interface TShadowSettingsStrings {
  insideShadow: string;
  offsetX: string;
  offsetY: string;
  blur: string;
  color: string;
  shadow: string;
  preview: string;
}

export interface TBasicColorsSectionStrings {
  gradientTitle: string;
  gradientStartCaption: string;
  gradientStopCaption: string;
  gradientTooltip: string;
  fontColorCaption: string;
}

export interface ILocaleMessages {
  savingSuccess: string;
  savingError: string;
}

export interface ILocale {
  core: {
    yes: string;
    no: string;
    leftToRight: string;
    rightToLeft: string;
    saveTooltip: string;
  };
  header: {
    version: string;
    dbVersion: string;
  };
  footer: {
    rights: string;
  };
  menu: {
    styles: string;
    newStyle: string;
    settings: string;
    themes: string;
  };
  appHeader: {
    styles: string;
    settings: string;
    editor: string;
    themes: string;
  };
  settings: {
    boxSettings: {
      boxSettingsCaption: string;
      marginsCaption: string;
      marginsCaptions: string[];
      marginsTooltip: string;
      borderSettings: {
        borderSettingsCaption: string;
        borderSettingsTooltip: string;
        borderStyleCaption: string;
        borderStyleValues: {
          solid: string;
          dashed: string;
          dotted: string;
          none: string;
        };
        borderWidthCaption: string;
        roundedCornersCaption: string;
        borderRadiusCaption: string;
      };
      collapsingCaption: string;
      collapsedCaption: string;
    };
    imagesSettings: {
      imagesSettingsCaption: string;
      imgMinusTitle: string;
      imgMinusCaption: string;
      imgMinusCheckCaption: string;
      imgMinusTooltip: string;
      imgPlusTitle: string;
      imgPlusCaption: string;
      imgPlusCheckCaption: string;
      imgPlusTooltip: string;
      durationCaption: string;
      durationTooltip: string;
      bigImgCaption: string;
      bigImgTooltip: string;
      showImgCaption: string;
      showImgTooltip: string;
      sideCaption: string;
    };
    textSettings: {
      caption: string;
      textFontCaption: string;
      textFontSizeLabel: string;
      textFontFamilyLabel: string;
      captionFontCaption: string;
      captionFontSizeLabel: string;
      captionFontFamilyLabel: string;
      fontSizeHelperText: string;
      fontFamilyHelperText: string;
      languageDirectionCaption: string;
      languageDirectionTooltip: string;
    };
    shadowsSettings: {
      caption: string;
      boxShadowCaption: string;
      boxShadowLabel: string;
      textShadowCaption: string;
      textShadowLabel: string;
      shadowSettings: TShadowSettingsStrings;
    };
    systemSettings: {
      caption: string;
      loadingCaption: string;
      staticCaption: string;
      clientDynamicCaption: string;
      serverDynamicCaption: string;
      staticTooltip: string;
      clientDynamicTooltip: string;
      serverDynamicTooltip: string;
    };
    deactivationSettings: {
      caption: string;
      actionsString: string;
      optionsCaption: string;
      dbCaption: string;
    };
  };
  styles: {
    filterAll: string;
    filterActive: string;
    filterTrash: string;
    itemAlias: string;
    itemType: string;
    noData: string;
  };
  editor: {
    namesSection: {
      caption: string;
      captionCaption: string;
      captionTooltip: string;
      nameCaption: string;
      nameTooltip: string;
      nameErrorTooltip: string;
      typeCaption: string;
      trashInTrash: string;
      trashActive: string;
    };
    borderImageSection: {
      caption: string;
      colorCaption: string;
      colorTooltip: string;
      imageTitle: string;
      imageCaption: string;
      imageCheckCaption: string;
      imageTooltip: string;
    };
    colorsSection: {
      bodyTitle: string;
      captionTitle: string;
      basicColorsSection: TBasicColorsSectionStrings;
    };
  };
  messages: {
    settings: ILocaleMessages;
    editor: ILocaleMessages;
    themes: ILocaleMessages;
  };
}
