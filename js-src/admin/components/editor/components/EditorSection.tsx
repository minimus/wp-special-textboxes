import React, { FC, ReactNode } from 'react';

import { EditorSectionBody, EditorSectionCaption, EditorSectionRoot } from '../styles';

interface TProps {
  caption: string;
  children: ReactNode | ReactNode[];
}

const EditorSection: FC<TProps> = (props) => {
  const { caption, children } = props;

  return (
    <EditorSectionRoot>
      <EditorSectionCaption>{caption}</EditorSectionCaption>
      <EditorSectionBody>{children}</EditorSectionBody>
    </EditorSectionRoot>
  );
};

export default EditorSection;
