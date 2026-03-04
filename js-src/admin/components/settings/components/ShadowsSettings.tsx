import React, { FC } from 'react';
import { useDispatch, useSelector } from 'react-redux';

import ShadowSettings from '../../../../ui-kit/ShadowSettings';
import { TDispatch, ILocale, TSettings, TShadowSettings } from '../../../../types/admin';
import { SETTINGS_SET_SETTINGS } from '../../../redux/constants';
import { TRootState } from '../../../redux';

import SettingsSection from './SettingsSection';

const ShadowsSettings: FC = () => {
  const dispatch: TDispatch = useDispatch();

  const settings: TSettings = useSelector((state: TRootState) => state.settings.settings);
  const localesData: ILocale | null = useSelector((state: TRootState) => state.locales.data);

  const {
    shadow,
    text: { shadow: textShadow },
    text,
  } = settings ?? {};

  const {
    settings: {
      shadowsSettings: {
        caption: captionString = '',
        boxShadowCaption = '',
        boxShadowLabel = '',
        textShadowCaption = '',
        textShadowLabel = '',
        shadowSettings = undefined,
      } = {},
    } = {},
  } = localesData ?? {};

  const onBoxShadowChange = (value: TShadowSettings): void => {
    dispatch({ type: SETTINGS_SET_SETTINGS, payload: { shadow: value } });
  };

  const onTextShadowChange = (value: TShadowSettings): void => {
    dispatch({ type: SETTINGS_SET_SETTINGS, payload: { text: { ...text, shadow: { ...value } } } });
  };

  return (
    <SettingsSection caption={captionString}>
      <ShadowSettings
        htmlId="box-shadow"
        caption={boxShadowCaption}
        label={boxShadowLabel}
        locale={shadowSettings}
        shadow={shadow}
        onChange={onBoxShadowChange}
      />
      <ShadowSettings
        htmlId="text-shadow"
        caption={textShadowCaption}
        label={textShadowLabel}
        locale={shadowSettings}
        shadow={textShadow}
        variant="text"
        onChange={onTextShadowChange}
      />
    </SettingsSection>
  );
};

export default ShadowsSettings;
