import {Period} from '../contexts/PeriodContext';

export function getCurrentPeriod(): Period {
  const currentDate = new Date();
  return {
    year: currentDate.getFullYear() - 1,
    period: currentDate.getMonth() < 5 ? 1 : 2,
  };
}
