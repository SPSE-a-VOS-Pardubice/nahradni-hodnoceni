import {Period} from '../contexts/PeriodContext';
import _Class from '../models/data/_Class';

export function formatClassRelativeToPeriod(_class: _Class, period: Period) {
  return `${period.year - _class.year + 1}.${_class.label}`;
}
