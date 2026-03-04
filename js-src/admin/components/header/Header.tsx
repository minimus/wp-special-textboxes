import React, { FC } from 'react';
import { useSelector } from 'react-redux';

import type { ILocale } from '../../../types/admin';
import { TRootState } from '../../redux';

import { HeaderRoot, SysInfoContainer } from './styles';

const Header: FC = () => {
  const sysInfo = useSelector((state: TRootState) => state.header.sysInfo);
  const localesData: ILocale | null = useSelector((state: TRootState) => state.locales.data);

  const { version = '', dbVersion = '' } = sysInfo ?? {};
  const { header = { version: 'Version', dbVersion: 'DB Version' } } = localesData ?? {};

  return (
    <HeaderRoot>
      <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24">
        <path d="M19 4H5c-1.11 0-2 .9-2 2v12c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.89-2-2-2zm0 14H5V8h14v10z" />
        <path fill="none" d="M0 0h24v24H0z" />
      </svg>
      <h1>Special Text Boxes</h1>
      {sysInfo !== null && (
        <SysInfoContainer>
          <span>{`${header.version}: ${version}`}</span>
          <span>{`${header.dbVersion}: ${dbVersion}`}</span>
        </SysInfoContainer>
      )}
    </HeaderRoot>
  );
};

export default Header;
