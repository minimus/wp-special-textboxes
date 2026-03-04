import { combineReducers, configureStore } from '@reduxjs/toolkit';

import header from './modules/header/header';
import settings from './modules/settings/settings';
import styles from './modules/styles/styles';
import editor from './modules/editor/editor';
import locales from './modules/locales/locales';
import themes from './modules/themes/themes';

declare const __DEV__: string | undefined;

const reducers = { settings, styles, editor, themes, header, locales };
const reducer = combineReducers(reducers);

const store = configureStore({
  reducer,
  devTools: !!__DEV__,
  middleware: (getDefaultMiddleware) =>
    getDefaultMiddleware({
      serializableCheck: false,
      immutableCheck: false,
    }),
});

export type TRootState = ReturnType<typeof store.getState>;
export type TRootStore = typeof store;
export type TRootGetState = typeof store.getState;
export type TRootDispatch = typeof store.dispatch;

export default store;
