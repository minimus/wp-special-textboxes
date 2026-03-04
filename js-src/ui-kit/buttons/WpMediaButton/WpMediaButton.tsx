import React, { useRef, FC } from 'react';
import IconButton from '@mui/material/IconButton';
import SearchIcon from '@mui/icons-material/Search';

import type { IWpMedia, TWpMediaAttachment } from '../../../types/admin';

interface TProps {
  disabled?: boolean;
  onSelect?: (value: string) => void;
}

interface IWindowWithMedia extends Window {
  wp?: {
    media: (m: Record<string, unknown>) => IWpMedia;
  };
}

const WpMediaButton: FC<TProps> = (props) => {
  const { disabled = false, onSelect } = props;

  const mediaRef = useRef(
    (window as IWindowWithMedia)?.wp?.media({
      /* title: mediaTexts.title,
            button: {
                text: mediaTexts.button,
            }, */
      multiple: false,
      state: 'library',
      library: {
        type: 'image',
      },
    }) ?? null,
  );

  const handleClick = (): void => {
    if (onSelect) {
      mediaRef.current ??=
        (window as IWindowWithMedia)?.wp?.media({
          multiple: false,
          state: 'library',
          library: {
            type: 'image',
          },
        }) ?? null;

      mediaRef?.current
        ?.on('select', () => {
          // eslint-disable-next-line @typescript-eslint/ban-ts-comment
          // @ts-expect-error
          const attachment: TWpMediaAttachment = mediaRef?.current?.state().get('selection').first().toJSON();
          const { url } = attachment;
          onSelect(url);
        })
        .open();
    }
  };

  return (
    <IconButton color="primary" size="medium" disabled={disabled} onClick={handleClick}>
      <SearchIcon />
    </IconButton>
  );
};

export default WpMediaButton;
