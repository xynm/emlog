<?php if (!defined('EMLOG_ROOT')) {
    exit('error!');
} ?>
<?php if (isset($_GET['active_del'])): ?><span class="alert alert-success">删除评论成功</span><?php endif; ?>
<?php if (isset($_GET['active_show'])): ?><span class="alert alert-success">审核评论成功</span><?php endif; ?>
<?php if (isset($_GET['active_hide'])): ?><span class="alert alert-success">隐藏评论成功</span><?php endif; ?>
<?php if (isset($_GET['active_edit'])): ?><span class="alert alert-success">修改评论成功</span><?php endif; ?>
<?php if (isset($_GET['active_rep'])): ?><span class="alert alert-success">回复评论成功</span><?php endif; ?>
<?php if (isset($_GET['error_a'])): ?><span class="alert alert-danger">请选择要执行操作的评论</span><?php endif; ?>
<?php if (isset($_GET['error_b'])): ?><span class="alert alert-danger">请选择要执行的操作</span><?php endif; ?>
<?php if (isset($_GET['error_c'])): ?><span class="alert alert-danger">回复内容不能为空</span><?php endif; ?>
<?php if (isset($_GET['error_d'])): ?><span class="alert alert-danger">内容过长</span><?php endif; ?>
<?php if (isset($_GET['error_e'])): ?><span class="alert alert-danger">评论内容不能为空</span><?php endif; ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">评论管理</h1>
    </div>
    <?php if ($hideCommNum > 0) :?>
        <div class="panel-heading">
            <ul class="nav nav-tabs">
                <li class="nav-item"><a class="nav-link <?php if ($hide == '') {
                        echo 'active';
                    } ?>" href="./comment.php?<?php echo $addUrl_1 ?>">全部</a></li>
                <li class="nav-item"><a class="nav-link <?php if ($hide == 'y') {
                        echo 'active';
                    } ?>" href="./comment.php?hide=y&<?php echo $addUrl_1 ?>">待审<?php
                        $hidecmnum = ROLE == ROLE_ADMIN ? $sta_cache['hidecomnum'] : $sta_cache[UID]['hidecommentnum'];
                        if ($hidecmnum > 0) echo '(' . $hidecmnum . ')';
                        ?></a>
                </li>
            </ul>
        </div>
    <?php endif; ?>
    <form action="comment.php?action=admin_all_coms" method="post" name="form_com" id="form_com">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <span class="badge badge-secondary">有<?php echo $cmnum; ?>条评论</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th></th>
                            <th>内容</th>
                            <th>评论人</th>
                            <th>时间</th>
                            <th>操作</th>
                            <th>所属文章</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if ($comment):
                            foreach ($comment as $key => $value):
                                $ishide = $value['hide'] == 'y' ? '<font color="red">[待审]</font>' : '';
                                $mail = !empty($value['mail']) ? "({$value['mail']})" : '';
                                $ip = !empty($value['ip']) ? "<br />来自IP：{$value['ip']}" : '';
                                $poster = !empty($value['url']) ? '<a href="' . $value['url'] . '">' . $value['poster'] . '</a>' : $value['poster'];
                                $value['content'] = str_replace('<br>', ' ', $value['content']);
                                $sub_content = subString($value['content'], 0, 50);
                                $value['title'] = subString($value['title'], 0, 42);
                                doAction('adm_comment_display');
                                ?>
                                <tr>
                                    <td width="19"><input type="checkbox" value="<?php echo $value['cid']; ?>" name="com[]" class="ids"/></td>
                                    <td width="350">
                                        <a href="#" data-toggle="modal" data-target="#exampleModal" data-cid="<?php echo $value['cid']; ?>"
                                           data-comment="<?php echo $value['content']; ?>">
                                        <?php echo $sub_content; ?>
                                        </a>
                                        <?php echo $ishide; ?>
                                    </td>
                                    <td class="small"><?php echo $poster; ?> <?php echo $mail; ?> <?php echo $ip; ?>
                                        <?php if (ROLE == ROLE_ADMIN): ?>
                                            <a href="javascript: em_confirm('<?php echo $value['ip']; ?>', 'commentbyip', '<?php echo LoginAuth::genToken(); ?>');"
                                               class="badge badge-pill badge-danger">按IP删除</a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="small"><?php echo $value['date']; ?></td>
                                    <td>
                                        <a href="javascript: em_confirm(<?php echo $value['cid']; ?>, 'comment', '<?php echo LoginAuth::genToken(); ?>');" class="care">删除</a>
                                        <?php if ($value['hide'] == 'y'): ?>
                                            <a href="comment.php?action=show&amp;id=<?php echo $value['cid']; ?>">审核</a>
                                        <?php else: ?>
                                            <a href="comment.php?action=hide&amp;id=<?php echo $value['cid']; ?>">隐藏</a>
                                        <?php endif; ?>
                                        <a href="comment.php?action=reply_comment&amp;cid=<?php echo $value['cid']; ?>">回复</a>
                                    </td>
                                    <td class="small"><a href="<?php echo Url::log($value['gid']); ?>"><?php echo $value['title']; ?></a></td>
                                </tr>
                            <?php endforeach; else: ?>
                            <tr>
                                <td class="tdcenter" colspan="4">还没有收到评论</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="list_footer">
                    <a href="javascript:void(0);" id="select_all">全选</a> 选中项：
                    <a href="javascript:commentact('del');" class="care">删除</a>
                    <a href="javascript:commentact('hide');">隐藏</a>
                    <a href="javascript:commentact('pub');">审核</a>
                    <input name="operate" id="operate" value="" type="hidden"/>
                </div>
                <div class="page"><?php echo $pageurl; ?></div>
            </div>
        </div>
    </form>
    <!--  模态窗  -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">回复评论</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="comment.php?action=doreply" method="post">
                    <div class="modal-body">
                        <p></p>
                        <div class="form-group">
                            <input type="hidden" value="<?php echo $commentId; ?>" name="cid"/>
                            <input type="hidden" value="<?php echo $gid; ?>" name="gid"/>
                            <input type="hidden" value="<?php echo $hide; ?>" name="hide"/>
                            <textarea class="form-control" id="reply" name="reply"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-primary">回复</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    setTimeout(hideActived, 2600);

    function commentact(act) {
        if (getChecked('ids') == false) {
            alert('请选择要操作的评论');
            return;
        }
        if (act == 'del' && !confirm('你确定要删除所选评论吗？')) {
            return;
        }
        $("#operate").val(act);
        $("#form_com").submit();
    }

    $("#menu_cm").addClass('active');

    //回复评论模态窗
    $('#exampleModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var comment = button.data('comment')
        var modal = $(this)
        modal.find('.modal-body p').html(comment)
    })

</script>
