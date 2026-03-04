import React, { useState, FC, ReactNode, useMemo } from 'react';
import classNames from 'classnames';

import type { TColors, TStyleColors } from '../../../types/admin';

interface TProps {
  caption: string;
  slug: string;
  colors: TStyleColors | TColors;
  children: ReactNode | ReactNode[] | string;
  captioned?: boolean;
  captionBg?: boolean;
}

const StyleBox: FC<TProps> = (props) => {
  const { caption, slug, colors, children, captioned = false, captionBg = false } = props;
  const { image: { image = '', defaultImage = '', enabled } = {} } = colors;

  const [collapsed, setCollapsed] = useState(false);

  /*const classes = captioned
    ? classNames('stb-container', `stb-style-${slug}`, 'stb-caption-box', collapsed && 'stb-collapsed')
    : classNames('stb-container', `stb-style-${slug}`, !captionBg && 'stb-no-caption');*/

  const classes = useMemo(
    () =>
      classNames('stb-container', `stb-style-${slug}`, { 'stb-caption-box': captioned, 'stb-collapsed': collapsed, 'stb-no-caption': !captionBg }),
    [slug, captioned, collapsed, captionBg],
  );

  const onClick = () => {
    setCollapsed(!collapsed);
  };

  return (
    <section className={classes}>
      <div className="stb-caption">
        <div className="stb-logo">
          <img className="stb-logo__image" src={enabled ? image : defaultImage} alt={slug} />
        </div>
        <div className="stb-caption-content">{caption}</div>
        <div className="stb-tool" role="button" onClick={onClick} />
      </div>
      <div className="stb-content">{children}</div>
    </section>
  );
};

export default StyleBox;
