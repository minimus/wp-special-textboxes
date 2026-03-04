import styled from 'styled-components';

interface IItemRootProps {
  image: string;
  isActive: boolean;
}

export const Root = styled.div`
  display: flex;
  flex-wrap: wrap;
  gap: 25px;
  margin: 25px;
`;

export const ItemRoot = styled.div<IItemRootProps>`
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  justify-content: flex-end;
  width: 450px;
  height: 300px;
  overflow: hidden;
  background-color: ${(props: IItemRootProps) => (props.isActive ? '#1976d2' : '#dcdada')};
  background-image: url(${(props: IItemRootProps) => props.image ?? ''});
  border: 1px solid ${(props: IItemRootProps) => (props.isActive ? '#1976d2' : '#dcdada')};
  border-radius: 5px;
  box-shadow: 0 0 5px 0 ${(props: IItemRootProps) => (props.isActive ? '#1976d2' : '#dcdada')};
`;

export const ItemInfo = styled.div`
  position: relative;
  width: 100%;
  height: 100px;
  padding: 10px;
  color: #fff;
  background-color: #0000009f;
`;
