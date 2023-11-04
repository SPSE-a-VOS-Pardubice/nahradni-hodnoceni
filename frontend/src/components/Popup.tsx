import {Popup as ReactJSPopup} from 'reactjs-popup';

const Popup = (props: {
  open: boolean;
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  children?: any;
  options: {
    text: string;
    onClick?: () => void;
  }[];
}) => {
  return (
    <ReactJSPopup
      open={props.open}
      modal
      closeOnDocumentClick={false}
      closeOnEscape={false}
    >
      <div className="popup active">
        <div className="popup_container">
          {props.children}
          <div className="decision_row">
            {props.options.map((option) => (
              <button
                onClick={() => option.onClick && setTimeout(option.onClick, 0)}
              >
                {option.text}
              </button>
            ))}
          </div>
        </div>
      </div>
    </ReactJSPopup>
  );
};

export default Popup;
