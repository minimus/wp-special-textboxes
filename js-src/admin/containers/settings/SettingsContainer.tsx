import React, { FC } from 'react';
import { useSelector } from 'react-redux';
import { Backdrop } from '@mui/material';

import Settings from '../../components/settings/Settings';
import { TRootState } from '../../redux';

const SettingsContainer: FC = () => {
  const saving: boolean = useSelector((state: TRootState) => state.settings.saving);

  return (
    <>
      <Settings />
      <Backdrop open={saving} invisible sx={{ color: '#fff', zIndex: 1000 }} />
    </>
  );
};

export default SettingsContainer;
