import React, { ChangeEvent, FC } from 'react';
import { TextField } from '@mui/material';
import { useSelector } from 'react-redux';

import { EditorSectionContentRow, TrashInfo, TypeInfo } from '../styles';
import { ILocale } from '../../../../types/admin';
import { TRootState } from '../../../redux';

import EditorSection from './EditorSection';

interface TProps {
  slug: string;
  type: string;
  caption: string;
  trash: boolean | number;
  slugIsValid: boolean;
  onChange: (val: Record<string, unknown>) => void;
}

const NamesSection: FC<TProps> = (props) => {
  const { slug, type, caption, trash, slugIsValid, onChange } = props;

  const localesData: ILocale | null = useSelector((state: TRootState) => state.locales.data);
  const {
    editor: {
      namesSection: {
        caption: captionString = '',
        captionCaption = '',
        captionTooltip = '',
        nameCaption = '',
        nameTooltip = '',
        nameErrorTooltip = '',
        typeCaption = '',
        trashInTrash = '',
        trashActive = '',
      } = {},
    } = {},
  } = localesData ?? {};

  const onValueChange =
    (key: string) =>
    (event: ChangeEvent<HTMLInputElement>): void => {
      if (onChange) {
        const { value } = event.target;
        onChange({ [key]: value });
      }
    };

  return (
    <EditorSection caption={captionString}>
      <EditorSectionContentRow style={{ padding: '5px 0' }}>
        <TextField
          id="box-caption"
          variant="outlined"
          value={caption}
          label={captionCaption}
          helperText={captionTooltip}
          fullWidth
          onChange={onValueChange('caption')}
        />
      </EditorSectionContentRow>
      <EditorSectionContentRow style={{ padding: '5px 0' }}>
        <TextField
          id="box-name"
          variant="outlined"
          value={slug}
          label={nameCaption}
          helperText={slugIsValid ? nameTooltip : nameErrorTooltip}
          error={!slugIsValid}
          fullWidth
          slotProps={{ input: { readOnly: type !== 'custom' || slug === 'custom' } }}
          onChange={onValueChange('slug')}
        />
      </EditorSectionContentRow>
      <TypeInfo>
        {typeCaption}: <span>{type}</span>
      </TypeInfo>
      <TrashInfo trash={trash}>{trash ? trashInTrash : trashActive}</TrashInfo>
    </EditorSection>
  );
};

export default NamesSection;
