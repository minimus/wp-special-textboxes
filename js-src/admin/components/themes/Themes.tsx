import React, { FC } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { useSnackbar } from 'notistack';

import { ILocale, TDispatch, TSettings, TThemeInfo } from '../../../types/admin';
import { activateTheme } from '../../redux/modules/themes/actions';
import { TRootState } from '../../redux';
import { STYLES_NEED_RELOAD } from '../../redux/constants';

import { Root } from './styles';
import ThemeItem from './components/ThemeItem';

const Themes: FC = () => {
  const dispatch: TDispatch = useDispatch();
  const { enqueueSnackbar } = useSnackbar();

  const themes: TThemeInfo[] = useSelector((state: TRootState) => state.themes.themes) ?? [];
  const settings: TSettings = useSelector((state: TRootState) => state.settings.settings);
  const localesData: ILocale | null = useSelector((state: TRootState) => state.locales.data);

  const { messages: { themes: messages = { savingSuccess: '', savingError: '' } } = {} } = localesData ?? {};

  const onActivateClick = (slug: string): void => {
    void activateTheme(slug)(dispatch, enqueueSnackbar, messages).then(() => {
      dispatch({ type: STYLES_NEED_RELOAD });
    });
  };

  return (
    <Root>
      {themes.map((item: TThemeInfo) => (
        <ThemeItem
          key={item.slug}
          name={item.name}
          slug={item.slug}
          description={item.description}
          image={item.image}
          active={item.slug === settings.themeName}
          onActivateClick={onActivateClick}
        />
      ))}
    </Root>
  );
};

export default Themes;
