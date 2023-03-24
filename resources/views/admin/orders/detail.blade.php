<x-admin title="Order-detail">
    <div class="row">
        <div class="col-12 mt-3">
            <h6 class="badge bg-info">Chi Tiết Đơn Hàng</h6>
            <!-- Striped Rows -->
            <table class="table table-striped table-hover table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên sản phảm</th>
                        <th>Ảnh</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order_detail as $item)
                    <tr style="vertical-align: middle;">
                        <td>{{$item->id}}</td>
                        <td>{{$item->product->name}}</td>
                        <td>
                            <img src="/storage/products/{{$item->product->image}}" width="100" height="100" alt="">
                        </td>
                        <td>{{number_format($item->product->price)}} <sup>đ</sup></td>
                        <td>{{$item->quantity}}</td>
                        <td>
                            <a href="{{route('admin.order_detail.delete',$item->id)}}" class="btn btn-sm btn-danger"><i
                                    class="ri-delete-bin-line"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-admin>