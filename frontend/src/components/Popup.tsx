import { Popup as ReactJSPopup } from "reactjs-popup"

const Popup = (props: {
    open: boolean,
    children?: any,
    options: {
        text: string,
        onClick?: () => void
    }[]
}) => {
    return (
        <ReactJSPopup open={props.open} modal closeOnDocumentClick={false} closeOnEscape={false}>
            <div className="repair_popup popup active">
                <div className="popup_container">
                    {props.children}
                    <div className="decision_row">
                        {props.options.map(option => (
                            <button onClick={_ => option.onClick && setTimeout(option.onClick, 0)}>{option.text}</button>
                        ))}
                    </div>
                </div>
            </div>
        </ReactJSPopup>
    )
}

export default Popup;
