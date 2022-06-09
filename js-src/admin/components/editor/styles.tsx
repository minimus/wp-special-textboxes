import styled from 'styled-components'

export const Root = styled.div`
	display: grid;
	grid-gap: 5px;
	grid-template-columns: 1fr 1fr;
	min-height: 200px;
	margin: 15px;

	@media (max-width: 1000px) {
		grid-template-columns: 1fr;
	}
`

export const EditorSectionRoot = styled.div`
	display: flex;
	flex-direction: column;
	align-items: flex-start;
	justify-content: flex-start;
	margin: 10px;
	padding: 0;
	border: 1px solid #dcdada;
	border-radius: 5px;
	box-shadow: 0 0 4px #0000001a;
`

export const EditorSectionCaption = styled.div`
	display: flex;
	align-items: center;
	justify-content: flex-start;
	width: 100%;
	padding: 5px 10px;
	font-weight: 700;
	border-bottom: 1px solid #dcdada;
	border-radius: 5px 5px 0 0;
`

export const EditorSectionBody = styled.div`
	display: flex;
	flex-direction: column;
	align-items: flex-start;
	justify-content: flex-start;
	width: 100%;
	padding: 10px;
`

export const EditorSectionContentRow = styled.div`
	display: flex;
	flex-direction: column;
	width: 100%;
`

export const EditorSectionPreviewContentRow = styled.div`
	display: grid;
	grid-template-columns: 70px 1fr;
`

type TPreviewContainerProps = {
	image: string
}

export const PreviewContainer = styled.div`
	background-image: url('${(props: TPreviewContainerProps) => props.image}');
	background-repeat: no-repeat;
	background-position: center;
	background-size: contain;
	border: 1px solid #dcdada;
	border-radius: 5px;
`

type TRowContentLineProps = {
	count: number
}

export const EditorSectionRowContentLine = styled.div`
	display: grid;
	gap: 15px;
	grid-template-columns: repeat(${(props: TRowContentLineProps) => props.count}, 1fr);

	@media (max-width: 1641px) {
		grid-template-columns: 1fr;
	}
`

export const TypeInfo = styled.p`
	padding-left: 5px;
	font-size: 17px;
	color: rgba(0, 0, 0, 0.6);

	& span {
		color: black;
	}
`

type TTrashInfoProps = {
	trash: boolean | number
}

export const TrashInfo = styled.p`
	padding-left: 5px;
	font-size: 17px;
	color: ${(props: TTrashInfoProps) => (props.trash ? 'red' : 'Green')};
`
