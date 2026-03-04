import React, { FC } from 'react';
import SaveIcon from '@mui/icons-material/Save';
import { useDispatch, useSelector, useStore } from 'react-redux';
import { useSnackbar } from 'notistack';
import { ProgressFab } from '@minimus/simplelib-ui-kit';

import { TDispatch, ILocale } from '../../../types/admin';
import { SETTINGS_SAVE_SETTINGS, STOP } from '../../redux/constants';
import { savePluginSettings } from '../../redux/modules/settings/actions';
import { TRootGetState, TRootState } from '../../redux';

import { Container, Root } from './styles';
import ImagesSettings from './components/ImagesSettings';
import BoxSettings from './components/BoxSettings';
import ShadowsSettings from './components/ShadowsSettings';
import TextSettings from './components/TextSettings';
import SystemSettings from './components/SystemSettings';
import DeactivationSettings from './components/DeactivationSettings';

const Settings: FC = () => {
  const dispatch: TDispatch = useDispatch();
  const store = useStore();

  const { enqueueSnackbar } = useSnackbar();

  const saving: boolean = useSelector((state: TRootState) => state.settings.saving);
  const savingSuccess: boolean = useSelector((state: TRootState) => state.settings.savingSuccess);
  const savingError: boolean = useSelector((state: TRootState) => state.settings.savingError);
  const localesData: ILocale | null = useSelector((state: TRootState) => state.locales.data);

  const { core: { saveTooltip = '' } = {} } = localesData ?? {};

  const onClick = (): void => {
    if (!saving) {
      // eslint-disable-next-line @typescript-eslint/unbound-method
      void savePluginSettings()(dispatch, store.getState as TRootGetState, enqueueSnackbar).then();
    }
  };

  const onDelay = () => {
    dispatch({ type: SETTINGS_SAVE_SETTINGS + STOP });
  };

  return (
    <Container>
      <Root>
        <BoxSettings />
        <ImagesSettings />
        <TextSettings />
        <ShadowsSettings />
        <SystemSettings />
        <DeactivationSettings />
      </Root>
      <ProgressFab
        processing={saving}
        success={savingSuccess}
        error={savingError}
        tooltip={saveTooltip}
        color="primary"
        displayPosition={{ bottom: 30, right: 40 }}
        delay={3000}
        onClick={onClick}
        onDelay={onDelay}
      >
        <SaveIcon />
      </ProgressFab>
    </Container>
  );
};

export default Settings;
