import { ILocale, TOptions, TSettings, TStyle, TSysInfo, TThemeInfo } from './admin';

interface IState {
  loading: boolean;
  loaded: boolean;
}

interface ISavableState extends IState {
  saving: boolean;
  savingSuccess: boolean;
  savingError: boolean;
}

export interface IWindowWithOptions extends Window {
  stbUserOptions?: TOptions;
}

// ** Settings START **
export interface ISettingsState extends Omit<ISavableState, 'loaded'> {
  settings: TSettings;
  options: TOptions;
}
// ** Settings END **

// ** header START **
export interface IHeaderState extends Omit<IState, 'loaded'> {
  sysInfo: TSysInfo | null;
}
// ** header END **

// ** Locales START **
export interface ILocaleState extends IState {
  data: ILocale | null;
}
// ** Locales END **

// ** Editor START **
export interface IEditorState extends Omit<ISavableState, 'loaded'> {
  style: TStyle | null;
}
// ** Editor END **

// ** Styles START **
export interface IStylesState extends Omit<IState, 'loaded'> {
  styles: TStyle[] | null;
  needReload: boolean;
  filter: number;
}
// ** Styles END **

// ** Themes START **
export interface IThemesState extends IState {
  themes: TThemeInfo[] | null;
  activatingSlug: string;
  activated: boolean;
  activationError: boolean;
}
// ** Themes END **

// ** Reducers START **
export interface IReducers {
  header: IHeaderState;
  settings: ISettingsState;
  styles: IStylesState;
  editor: IEditorState;
  themes: IThemesState;
  locales: ILocaleState;
}
// ** Reducers END

// ** ACTIONS START **
export interface IResponse {
  result?: string;
  success?: boolean;
  data?: Record<string, unknown>;
  settings?: Record<string, unknown>;
  completed?: number | boolean | Record<string, unknown>;
  deleted?: number;
}
// ** ACTIONS END **
