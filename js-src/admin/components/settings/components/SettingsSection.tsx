import React, { FC, ReactNode } from 'react';

import { SettingsSectionRoot, SettingsSectionBody, SettingsSectionCaption } from '../styles';

interface TProps {
  caption: string;
  children: ReactNode | ReactNode[];
}

const SettingsSection: FC<TProps> = (props) => {
  const { caption, children } = props;

  return (
    <SettingsSectionRoot>
      <SettingsSectionCaption>{caption}</SettingsSectionCaption>
      <SettingsSectionBody>{children}</SettingsSectionBody>
    </SettingsSectionRoot>
  );
};

export default SettingsSection;
