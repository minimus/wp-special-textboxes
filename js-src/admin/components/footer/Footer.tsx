import React, { FC } from 'react';
import { useSelector } from 'react-redux';

import type { ILocale } from '../../../types/admin';
import { TRootState } from '../../redux';

import { FooterRoot } from './styles';

const Footer: FC = () => {
  const localesData: ILocale | null = useSelector((state: TRootState) => state.locales.data);
  const { footer } = localesData ?? {};

  const currYear = new Date().getFullYear();
  const footerText = `Special Text Boxes for Wordpress. Copyright © 2010 - ${currYear}, `;
  const footerText2 = footer?.rights;
  const author = 'minimus';
  return (
    <FooterRoot>
      <span className="copyright-container">
        {footerText}
        <a href="https://www.simplelib.com/" target="_blank" rel="noopener noreferrer">
          {author}
        </a>
        . {footerText2}
      </span>
    </FooterRoot>
  );
};

export default Footer;
