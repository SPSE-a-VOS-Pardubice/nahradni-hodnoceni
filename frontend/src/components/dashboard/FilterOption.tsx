import {FormattedMessage} from 'react-intl';
import './Option.css';

const FilterOption = (props: {
    label: string,
    options: {[key: string]: string},
    selectedOption?: string,
    // eslint-disable-next-line no-unused-vars
    onChange?: (value?: string) => void
}) => {
  return (
    <button className={'select' + (props.selectedOption === undefined ? '' : ' selected')}>
      <span onClick={() => props.onChange && setTimeout(props.onChange, 0, undefined)}>{props.selectedOption === undefined ? props.label : (<FormattedMessage id={props.options[props.selectedOption]} />)}<i className="fa-solid fa-angle-down"></i></span>
      <div className="dropdown">
        {Object.entries(props.options).map(option => (
          <option key={option[0]} value={option[0]} onClick={() => props.onChange && setTimeout(props.onChange, 0, option[0])}><FormattedMessage id={option[1]} /></option>
        ))}
      </div>
    </button>
  );
};

export default FilterOption;
