import {FormattedMessage} from 'react-intl';
import './Option.css';
import {GroupByOptions} from '../../models/ExamDisplayRestrictions';
import classNames from 'classnames';

const GroupOption = (props: {
    label: string,
    groupBy?: GroupByOptions,
    // eslint-disable-next-line no-unused-vars
    onChange?: (sortBy?: GroupByOptions) => void
}) => {
  return (
    <button className={classNames('select', {selected: props.groupBy})}>
      <span onClick={() => props.onChange && setTimeout(props.onChange, 0, undefined)}>{props.groupBy === undefined ? props.label : (<FormattedMessage id={`groupBy.${props.groupBy}`} />)}<i className="fa-solid fa-angle-down"></i></span>
      <div className="dropdown">
        {Array.from(['student', 'examiner', 'student._class'] as GroupByOptions[], option => (
          <option key={option} value={option} onClick={() => props.onChange && setTimeout(props.onChange, 0, option)}><FormattedMessage id={`groupBy.${option}`} /></option>
        ))}
      </div>
    </button>
  );
};

export default GroupOption;
