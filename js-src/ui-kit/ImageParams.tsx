import React, { FC, ReactNode } from 'react';
import { TextInput, SwitchInput } from '@minimus/simplelib-ui-kit';

import { FieldCaption, ImageComponentsContainer, ImageInputsContainer, ImagePreview, Root } from './styles';

interface TProps {
  id: string;
  title: string;
  caption: string;
  checkCaption: string;
  tooltip?: string | ReactNode;
  value: string;
  check?: boolean;
  defaultImage?: string;
  preview?: number | boolean;
  size?: 'xSmall' | 'small' | 'half' | 'big' | 'fill';
  suffix?: string | ReactNode;
  inside?: boolean;
  disabled?: boolean;
  button?: boolean;
  onChange?: (val: string | number) => void;
  onCheck?: (value: boolean | 0 | 1) => void;
}

const ImageParams: FC<TProps> = (props) => {
  const {
    id,
    title,
    caption,
    checkCaption,
    tooltip,
    value,
    check = false,
    defaultImage = '',
    suffix,
    button = false,
    disabled = false,
    size = 'fill',
    preview = false,
    inside = true,
    onChange,
    onCheck,
  } = props;

  return (
    <Root size={size} inside={inside} button={button}>
      <FieldCaption style={{ margin: '0 5px' }}>{title}</FieldCaption>
      <ImageComponentsContainer preview={preview}>
        <ImagePreview image={check ? defaultImage : value} $size={preview ?? false} />
        <ImageInputsContainer>
          <TextInput
            id={id}
            contentType="string"
            value={value}
            size={size}
            caption={caption}
            tooltip={tooltip}
            suffix={suffix}
            button={button}
            disabled={check && disabled}
            inside={inside}
            onChange={onChange}
          />
          <SwitchInput id={`${id}-check`} value={check} caption={checkCaption} onChange={onCheck} />
        </ImageInputsContainer>
      </ImageComponentsContainer>
    </Root>
  );
};

export default ImageParams;
