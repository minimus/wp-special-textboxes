import React, { useEffect, FC } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { Backdrop } from '@mui/material';

import type { TDispatch } from '../../../types/admin';
import { getThemesInfo } from '../../redux/modules/themes/actions';
import Themes from '../../components/themes/Themes';
import { TRootState } from '../../redux';

const ThemesContainer: FC = () => {
  const dispatch: TDispatch = useDispatch();

  const loading: boolean = useSelector((state: TRootState) => state.themes.loading);
  const loaded: boolean = useSelector((state: TRootState) => state.themes.loaded);

  useEffect(() => {
    if (!loading && !loaded) {
      void getThemesInfo()(dispatch).then();
    }
  }, [dispatch, loaded, loading]);

  return (
    <>
      <Themes />
      <Backdrop open={loading} sx={{ color: '#fff', zIndex: 1000 }} />
    </>
  );
};

export default ThemesContainer;
