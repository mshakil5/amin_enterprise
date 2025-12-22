@extends('admin.layouts.admin')

@section('content')
    <section class="content pt-3">
        <div class="container-fluid">

            <div class="row justify-content-center">
                <div class="col-md-10">

                    <div class="card card-secondary shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fa fa-undo mr-1"></i>
                                {{ $reverse ? 'Update Reverse Transaction' : 'Create Reverse Transaction' }}
                            </h3>
                        </div>

                        <form method="POST" action="{{ route('admin.transactions.reverse.save') }}">
                            @csrf

                            <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">

                            <div class="card-body">
                                <div class="card mb-3 border-info">
                                    <div class="card-header bg-info text-white">
                                        <strong>Original Transaction</strong>
                                    </div>
                                    <div class="card-body">

                                        <div class="row">
                                            <div class="col-md-3">
                                                <label>Date</label>
                                                <input class="form-control" value="{{ $transaction->date }}" readonly>
                                            </div>

                                            <div class="col-md-3">
                                                <label>Account</label>
                                                <input class="form-control"
                                                    value="{{ $transaction->chartOfAccount->account_name ?? '' }}" readonly>
                                            </div>

                                            <div class="col-md-3">
                                                <label>Transaction Type</label>
                                                <input class="form-control" value="{{ $transaction->tran_type }}" readonly>
                                            </div>

                                            <div class="col-md-3">
                                                <label>Payment Type</label>
                                                <input class="form-control" value="{{ $transaction->payment_type }}"
                                                    readonly>
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-md-4">
                                                <label>Reference</label>
                                                <input class="form-control" value="{{ $transaction->ref }}" readonly>
                                            </div>

                                            <div class="col-md-4">
                                                <label>Amount</label>
                                                <input class="form-control text-danger font-weight-bold"
                                                    value="{{ number_format($transaction->amount, 2) }}" readonly>
                                            </div>

                                            <div class="col-md-4">
                                                <label>Description</label>
                                                <input class="form-control" value="{{ $transaction->description }}"
                                                    readonly>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <strong>Reverse Transaction</strong>
                                    </div>
                                    <div class="card-body">

                                        <div class="row">
                                            <div class="col-md-12">
                                                <label>Date*</label>
                                                <input type="date" name="date" class="form-control"
                                                    value="{{ $reverse->date ?? '' }}" required>
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-md-12">
                                                <label>Note</label>
                                                <textarea name="note" class="form-control" rows="3">{{ $reverse->note ?? '' }}</textarea>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>

                            <div class="card-footer text-right">
                                <a href="{{ url()->previous() }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>

                                <button class="btn btn-success">
                                    <i class="fa fa-save"></i>
                                    {{ $reverse ? 'Update Reverse' : 'Save Reverse' }}
                                </button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection