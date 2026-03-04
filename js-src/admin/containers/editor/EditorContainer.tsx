import React, { useEffect, FC } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { useParams } from 'react-router-dom';

import type { TDispatch, TStyle } from '../../../types/admin';
import { getEditorData } from '../../redux/modules/editor/actions';
import Editor from '../../components/editor/Editor';
import { TRootState } from '../../redux';

interface TProps {
  newStyle?: boolean;
}

const EditorContainer: FC<TProps> = (props) => {
  const { newStyle = false } = props;

  const dispatch: TDispatch = useDispatch();

  const { slug } = useParams();

  const style: TStyle | null = useSelector((state: TRootState) => state.editor.style);

  useEffect(() => {
    if (slug) {
      void getEditorData(slug)(dispatch).then();
    }
  }, [dispatch, slug]);

  if (!style) return null;

  return <Editor newStyle={newStyle} />;
};

export default EditorContainer;
