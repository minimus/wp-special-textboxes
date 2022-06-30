import styled from 'styled-components'

type TItemRootProps = {
	image: string
	isActive: boolean
}

export const Root = styled.div`
	display: flex;
	flex-wrap: wrap;
	gap: 25px;
	margin: 25px;
`

export const ItemRoot = styled.div`
	display: flex;
	flex-direction: column;
	align-items: flex-end;
	justify-content: flex-end;
	width: 450px;
	height: 300px;
	background-color: ${(props: TItemRootProps) => (props.isActive ? '#1976d2' : '#dcdada')};
	background-image: url(${(props: TItemRootProps) => props.image ?? ''});
	box-sizing: border-box;
	border: 1px solid ${(props: TItemRootProps) => (props.isActive ? '#1976d2' : '#dcdada')};
	border-radius: 5px;
	overflow: hidden;
	box-shadow: 0 0 5px 0 ${(props: TItemRootProps) => (props.isActive ? '#1976d2' : '#dcdada')};
`

export const ItemInfo = styled.div`
	position: relative;
	width: 100%;
	height: 100px;
	padding: 10px;
	color: #ffffff;
	background-color: #0000009f;
`
