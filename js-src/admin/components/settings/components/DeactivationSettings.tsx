import React, { FC } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { SwitchInput } from '@minimus/simplelib-ui-kit';

import { TDispatch, ILocale, TSettings } from '../../../../types/admin';
import { FieldCaption } from '../../../../ui-kit/styles';
import { SETTINGS_SET_SETTINGS } from '../../../redux/constants';
import { TRootState } from '../../../redux';

import SettingsSection from './SettingsSection';

const DeactivationSettings: FC = () => {
  const dispatch: TDispatch = useDispatch();

  const settings: TSettings = useSelector((state: TRootState) => state.settings.settings);
  const localesData: ILocale | null = useSelector((state: TRootState) => state.locales.data);

  const { deleteOptions, deleteDB } = settings;
  const { settings: { deactivationSettings: { caption: captionString = '', actionsString = '', optionsCaption = '', dbCaption = '' } = {} } = {} } =
    localesData ?? {};

  const onChange =
    (key: string) =>
    (value: boolean): void => {
      dispatch({ type: SETTINGS_SET_SETTINGS, payload: { [key]: value } });
    };

  return (
    <SettingsSection caption={captionString}>
      <FieldCaption style={{ margin: '0 5px' }}>{actionsString}</FieldCaption>
      <SwitchInput id="deleteOptions" value={deleteOptions} caption={optionsCaption} onChange={onChange('deleteOptions')} />
      <SwitchInput id="deleteDB" value={deleteDB} caption={dbCaption} onChange={onChange('deleteDB')} />
    </SettingsSection>
  );
};

export default DeactivationSettings;
