import React, { useEffect, useState, FC, useEffectEvent } from 'react';
import { useLocation } from 'react-router-dom';
import Box from '@mui/material/Box';
import AppBar from '@mui/material/AppBar';
import Toolbar from '@mui/material/Toolbar';
import Typography from '@mui/material/Typography';
import { useSelector } from 'react-redux';

import type { ILocale } from '../../../types/admin';
import { TRootState } from '../../redux';

import AppMenu from './AppMenu';

const AppHeader: FC = () => {
  const localesData: ILocale | null = useSelector((state: TRootState) => state.locales.data);
  const { appHeader: { styles = '', settings = '', editor = '', themes = '' } = {} } = localesData ?? {};

  const location = useLocation();
  const { pathname } = location;

  const [title, seTitle] = useState<string>('');

  const pageName = (path: string) => {
    if (path === '/') {
      return styles;
    }
    if (path === '/settings') {
      return settings;
    }
    if (/\/editor.*/i.test(path)) {
      return editor;
    }
    if (/\/themes.*/i.test(path)) {
      return themes;
    }
    return 'WTF';
  };

  const updateTitle = useEffectEvent(() => {
    seTitle(pageName(pathname));
  });

  useEffect(() => {
    updateTitle();
  }, [pathname, styles, settings, editor, themes]);

  return (
    <Box sx={{ flexGrow: 1 }}>
      <AppBar position="static">
        <Toolbar>
          <AppMenu />
          <Typography variant="h6" sx={{ flexGrow: 1 }}>
            {title}
          </Typography>
        </Toolbar>
      </AppBar>
    </Box>
  );
};

export default AppHeader;
