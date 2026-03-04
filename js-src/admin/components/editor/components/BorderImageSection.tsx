import React, { FC, useCallback } from 'react';
import { useSelector } from 'react-redux';
import { ColorsInput } from '@minimus/simplelib-ui-kit';

import { EditorSectionContentRow } from '../styles';
import type { TElementColors, TImageSettings, ILocale } from '../../../../types/admin';
import ImageParams from '../../../../ui-kit/ImageParams';
import WpMediaButton from '../../../../ui-kit/buttons/WpMediaButton/WpMediaButton';
import { IReducers } from '../../../../types/state';

import EditorSection from './EditorSection';

interface TProps {
  border: TElementColors;
  image: TImageSettings;
  onBorderChange: (val: Record<string, unknown>) => void;
  onImageChange: (val: Record<string, unknown>) => void;
}

const BorderImageSection: FC<TProps> = (props) => {
  const { border, image, onBorderChange, onImageChange } = props;

  const localesData: ILocale | null = useSelector((state: IReducers) => state.locales.data);
  const {
    editor: {
      borderImageSection: {
        caption: captionString = '',
        colorCaption = '',
        colorTooltip = '',
        imageTitle = '',
        imageCaption = '',
        imageCheckCaption = '',
        imageTooltip = '',
      } = {},
    } = {},
  } = localesData ?? {};

  const { color } = border;
  const { image: imageUrl, defaultImage, enabled } = image;

  const onBorderValueChange =
    (key: string) =>
    (value: string): void => {
      if (onBorderChange) {
        onBorderChange({ [key]: value });
      }
    };

  const onImageValueChange = useCallback(
    (value: string | number): void => {
      onImageChange?.({ image: value });
    },
    [onImageChange],
  );

  const onImageCheckChange = useCallback(
    (value: boolean | 0 | 1) => {
      onImageChange?.({ enabled: !value });
    },
    [onImageChange],
  );

  return (
    <EditorSection caption={captionString}>
      <EditorSectionContentRow style={{ padding: '5px 0' }}>
        <ColorsInput id="border-color" value={color} caption={colorCaption} tooltip={colorTooltip} onChange={onBorderValueChange('color')} />
      </EditorSectionContentRow>
      <EditorSectionContentRow>
        <ImageParams
          id="style-icon"
          value={imageUrl}
          check={!enabled}
          defaultImage={defaultImage}
          preview={50}
          title={imageTitle}
          caption={imageCaption}
          checkCaption={imageCheckCaption}
          tooltip={imageTooltip}
          suffix={<WpMediaButton disabled={!enabled} onSelect={onImageValueChange} />}
          size="fill"
          button
          onChange={onImageValueChange}
          onCheck={onImageCheckChange}
        />
      </EditorSectionContentRow>
    </EditorSection>
  );
};

export default BorderImageSection;
