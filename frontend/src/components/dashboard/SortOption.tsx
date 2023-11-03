import {FormattedMessage} from 'react-intl';
import './Option.css';
import {DataOptions} from '../../models/ExamDisplayRestrictions';

// const orderOptions = propsToMapWithPrefix("", [
//     "sort.student",
//     "sort.student.reverse",
//     "sort.teacher",
//     "sort.teacher.reverse",
//     "sort.class",
//     "sort.class.reverse",
//     "sort.mark",
//     "sort.mark.reverse"
// ]);

function toId(sortBy?: DataOptions, reverse?: boolean): string {
  return reverse === true ? `sort.${sortBy}.reverse` : `sort.${sortBy}`;
}

const orderOptions = new Map(
  (['student', 'teacher', 'class', 'mark'] as DataOptions[])
      .flatMap(sortBy => [
        [toId(sortBy), {sortBy: sortBy, reverse: false}],
        [toId(sortBy, true), {sortBy: sortBy, reverse: true}],
      ]),
);

const SortOption = (props: {
    label: string,
    sortBy?: DataOptions,
    reverse?: boolean,
    // eslint-disable-next-line no-unused-vars
    onChange?: (sortBy?: DataOptions, reverse?: boolean) => void
}) => {
  return (
    <button className={'select' + (props.sortBy === undefined ? '' : ' selected')}>
      <span onClick={() => props.onChange && setTimeout(props.onChange, 0, undefined)}>{props.sortBy === undefined ? props.label : (<FormattedMessage id={toId(props.sortBy, props.reverse)} />)}<i className="fa-solid fa-angle-down"></i></span>
      <div className="dropdown">
        {Array.from(orderOptions.entries(), option => (
            <option key={option[0]} value={option[0]} onClick={() => props.onChange && setTimeout(props.onChange, 0, option[1].sortBy, option[1].reverse)}><FormattedMessage id={option[0]} /></option>
        ))}
      </div>
    </button>
  );
};

export default SortOption;
