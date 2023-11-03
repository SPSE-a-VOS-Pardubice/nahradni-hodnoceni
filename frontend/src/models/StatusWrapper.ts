
type StatusWrapper<T> = {
  id: 'FETCHING',
} | {
  id: 'FAILED',
  message: string
} | {
  id: 'SUCCESS',
  content: T,
}

export default StatusWrapper;
